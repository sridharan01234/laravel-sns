<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign;
use App\Models\Template;
use App\Models\Customer;
use Aws\Sns\SnsClient;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $sns;

    public function __construct(Message $message)
    {
        $this->message = Message::where('id', $message->id)->first();
        
        $this->sns = new SnsClient([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function handle()
    {
        try {
            // Refresh the model to ensure we have the latest data
            $this->message = Message::where('id', $this->message->id)->first();
    
            if (!$this->message->campaign_id) {
                throw new \Exception('No campaign ID assigned to message ID: ' . $this->message->id);
            }
    
            // Get campaign without relation
            $campaign = Campaign::where('id', $this->message->campaign_id)->first();
            if (!$campaign) {
                throw new \Exception('Campaign not found for message ID: ' . $this->message->id);
            }

            // Get template without relation
            $template = Template::where('id', $campaign->template_id)->first();            
            if (!$template) {
                throw new \Exception('Template not found for campaign ID: ' . $campaign->id);
            }
            
            // Get customer without relation
            $customer = Customer::where('id', $this->message->customer_id)->first();
            if (!$customer) {
                throw new \Exception('Customer not found for message ID: ' . $this->message->id);
            }

            // Prepare variables
            $variables = $this->prepareMessageVariables($customer, $template);

            // Send the message via SNS
            $response = $this->sendViaSNS($customer->mobile, $template, $variables);

            if (!$response['success']) {
                throw new \Exception($response['error']);
            }

            // Update message status
            $this->message->update([
                'status' => 'sent',
                'variables' => $variables,
                'response' => $response,
                'sent_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Message Sending Failed: ' . $e->getMessage(), [
                'message_id' => $this->message->id,
                'customer_id' => $this->message->customer_id ?? null,
                'campaign_id' => $this->message->campaign_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->message->update([
                'status' => 'failed',
                'response' => ['error' => $e->getMessage()]
            ]);

            throw $e;
        }
    }

    protected function sendViaSNS($phoneNumber, $template, $variables)
    {
        try {
            // Format the message using template and variables
            $messageText = $this->formatMessage($template, $variables);

            // Ensure phone number is in E.164 format
            $formattedPhoneNumber = $this->formatPhoneNumber($phoneNumber);

            // Send SMS via SNS
            $result = $this->sns->publish([
                'Message' => $messageText,
                'PhoneNumber' => $formattedPhoneNumber,
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Transactional' // Use 'Promotional' for marketing messages
                    ],
                    'AWS.SNS.SMS.SenderID' => [
                        'DataType' => 'String',
                        'StringValue' => env('AWS_SNS_SENDER_ID', 'MYSENDER')
                    ]
                ]
            ]);

            return [
                'success' => true,
                'message_id' => $result['MessageId'] ?? null,
                'sent_at' => now(),
                'provider' => 'aws-sns'
            ];

        } catch (\Exception $e) {
            Log::error('SNS Sending Failed: ' . $e->getMessage(), [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'aws-sns'
            ];
        }
    }

    protected function formatMessage($template, $variables)
    {
        $messageText = $template->content;
        foreach ($variables as $key => $value) {
            $messageText = str_replace('{' . $key . '}', $value, $messageText);
        }
        return $messageText;
    }

    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add + prefix if not present
        if (substr($cleaned, 0, 1) !== '+') {
            $cleaned = '+' . $cleaned;
        }
        
        return $cleaned;
    }

    protected function prepareMessageVariables($customer, $template)
    {
        $variables = [
            'customer_name' => $customer->name,
            'mobile' => $customer->mobile,
        ];

        // Add plan-related variables if available
        if ($customer->plan_id) {
            $plan = Plan::where('id', $customer->plan_id)->first();
            if ($plan) {
                $variables = array_merge($variables, [
                    'plan_name' => $plan->name,
                    'amount' => $plan->amount,
                    'expiry_date' => $customer->plan_expiry?->format('d M, Y'),
                    'remaining_days' => now()->diffInDays($customer->plan_expiry, false)
                ]);
            }
        }

        // Add any custom variables from the template
        if($template->variables) {
            foreach ($template->variables as $variable) {
                if (!isset($variables[$variable])) {
                    $variables[$variable] = ''; // Set default empty value for undefined variables
                }
            }
        }

        return $variables;
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Message Job Failed: ' . $exception->getMessage(), [
            'message_id' => $this->message->id,
            'customer_id' => $this->message->customer_id ?? null,
            'campaign_id' => $this->message->campaign_id ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        $this->message->update([
            'status' => 'failed',
            'response' => [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]
        ]);
    }
}
