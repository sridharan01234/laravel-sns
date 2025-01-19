<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Aws\Sns\SnsClient;
use Illuminate\Support\Facades\Http;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $provider;

    public function __construct(Message $message, $provider = 'aws')
    {
        $this->message = $message;
        $this->provider = $provider;
    }

    public function handle()
    {
        try {
            $this->message->load(['customer', 'campaign.template']);
            
            $customer = $this->message->customer;
            $template = $this->message->campaign->template;

            if (!$customer || !$template) {
                throw new \Exception('Missing required data: customer or template not found');
            }

            $formattedPhone = $this->formatPhoneNumber($customer->mobile);
            $messageText = $this->prepareMessageText($template->content, $customer);

            // Send based on provider
            $result = $this->provider === 'aws' 
                ? $this->sendViaSNS($formattedPhone, $messageText)
                : $this->sendVia2Factor($formattedPhone, $messageText);

            $this->updateMessageStatus('sent', $result);
            
        } catch (\Exception $e) {
            $this->handleError($e);
            throw $e;
        }
    }

    protected function handleError($exception)
    {
        $this->message->update([
            'status' => 'failed',
            'response' => [
                'error' => $exception->getMessage()
            ]
        ]);

        Log::error('Message sending failed', [
            'message_id' => $this->message->id,
            'error' => $exception->getMessage()
        ]);
    }

    protected function sendViaSNS($phone, $message)
    {
        $sns = new SnsClient([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'http' => ['verify' => false]
        ]);

        $result = $sns->publish([
            'Message' => $message,
            'PhoneNumber' => $phone,
            'MessageAttributes' => [
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' => 'Transactional'
                ]
            ]
        ]);

        return [
            'provider' => 'aws',
            'message_id' => $result['MessageId'] ?? null,
        ];
    }

    protected function sendVia2Factor($phone, $message)
    {
        $apiUrl = 'https://2factor.in/API/R1/';
        
        $formattedPhone = ltrim($phone, '+');
        if (!str_starts_with($formattedPhone, '91')) {
            $formattedPhone = '91' . ltrim($formattedPhone, '0');
        }
    
        $formData = [
            'module' => 'TRANS_SMS',
            'apikey' => env('TWO_FACTOR_API_KEY'),
            'to' => $formattedPhone,
            'from' => env('TWO_FACTOR_SENDER_ID'),
            'msg' => $message
        ];
    
        Log::debug('Attempting 2Factor API request', [
            'url' => $apiUrl,
            'payload' => array_merge(
                $formData,
                ['msg' => substr($message, 0, 30) . '...']
            )
        ]);
    
        try {
            // Added withOptions to handle SSL verification
            $response = Http::withOptions([
                'verify' => false  // Disable SSL verification - Use only in development
            ])
            ->asForm()
            ->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])
            ->post($apiUrl, $formData);
    
            Log::debug('2Factor API raw response', [
                'status_code' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);
    
            $responseData = $response->json();
    
            if (!$response->successful() || ($responseData['Status'] ?? '') === 'Error') {
                throw new \Exception('2Factor API error: ' . $response->body());
            }
    
            return [
                'provider' => '2factor',
                'message_id' => $responseData['Details'] ?? null,
                'status' => $responseData['Status'] ?? 'Unknown'
            ];
    
        } catch (\Exception $e) {
            Log::error('2Factor API request failed', [
                'error' => $e->getMessage(),
                'url' => $apiUrl,
                'payload' => array_merge(
                    $formData,
                    ['msg' => substr($message, 0, 30) . '...']
                )
            ]);
            
            throw $e;
        }
    }
    

    protected function updateMessageStatus($status, $result)
    {
        $this->message->update([
            'status' => $status,
            'response' => [
                'provider' => $result['provider'],
                'message_id' => $result['message_id'],
                'sent_at' => now()
            ],
            'sent_at' => now()
        ]);

        Log::info('Message sent successfully', [
            'message_id' => $this->message->id,
            'provider' => $result['provider'],
            'provider_message_id' => $result['message_id']
        ]);
    }

    protected function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if not present
        if (strlen($number) === 10) {
            $number = '+91' . $number;
        } elseif (strlen($number) === 12 && substr($number, 0, 2) === '91') {
            $number = '+' . $number;
        }
        
        return $number;
    }

    protected function prepareMessageText($template, $customer)
    {
        $variables = [
            '{name}' => $customer->name ?? '',
            '{mobile}' => $customer->mobile ?? '',
            '{email}' => $customer->email ?? '',
            // Add more variables as needed
        ];

        return str_replace(
            array_keys($variables),
            array_values($variables),
            $template
        );
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Message Job Failed', [
            'message_id' => $this->message->id,
            'error' => $exception->getMessage()
        ]);

        $this->message->update([
            'status' => 'failed',
            'response' => ['error' => $exception->getMessage()]
        ]);
    }
}
