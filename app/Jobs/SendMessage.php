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
    protected $topicArn;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function handle()
    {
        $this->sns = new SnsClient([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'http' => [
                'verify' => false
            ]
        ]);

        try {
            // Create or get existing topic
            $topicArn = $this->createOrGetTopic();

            // Get campaign and template data
            $campaign = Campaign::findOrFail($this->message->campaign_id);
            $template = Template::findOrFail($campaign->template_id);
            $customer = Customer::findOrFail($this->message->customer_id);

            // Subscribe customer to topic
            $this->subscribeToTopic($topicArn, $customer->mobile);

            // Prepare and send message
            $variables = $this->prepareMessageVariables($customer, $template);
            $messageText = $this->formatMessage($template, $variables);

            $response = $this->publishToTopic($topicArn, $messageText);

            // Update message status
            $this->message->update([
                'status' => 'sent',
                'variables' => $variables,
                'response' => $response,
                'sent_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('SMS Campaign Failed', [
                'message_id' => $this->message->id,
                'error' => $e->getMessage()
            ]);

            $this->message->update([
                'status' => 'failed',
                'response' => ['error' => $e->getMessage()]
            ]);

            throw $e;
        }
    }

    protected function createOrGetTopic()
    {
        try {
            // Create unique topic name based on campaign
            $topicName = 'campaign-' . $this->message->campaign_id;

            $result = $this->sns->createTopic([
                'Name' => $topicName,
                'Tags' => [
                    [
                        'Key' => 'campaign_id',
                        'Value' => (string)$this->message->campaign_id
                    ]
                ]
            ]);

            Log::info('SNS Topic Created', [
                'topic_arn' => $result['TopicArn'],
                'campaign_id' => $this->message->campaign_id
            ]);

            return $result['TopicArn'];

        } catch (\Exception $e) {
            Log::error('Failed to create SNS topic', [
                'campaign_id' => $this->message->campaign_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function subscribeToTopic($topicArn, $phoneNumber)
    {
        try {
            $formattedPhoneNumber = $this->formatPhoneNumber($phoneNumber);

            $result = $this->sns->subscribe([
                'TopicArn' => $topicArn,
                'Protocol' => 'sms',
                'Endpoint' => $formattedPhoneNumber,
                'ReturnSubscriptionArn' => true
            ]);

            Log::info('Phone Subscribed to Topic', [
                'phone' => $formattedPhoneNumber,
                'topic_arn' => $topicArn,
                'subscription_arn' => $result['SubscriptionArn']
            ]);

            return $result['SubscriptionArn'];

        } catch (\Exception $e) {
            Log::error('Failed to subscribe to topic', [
                'phone' => $phoneNumber,
                'topic_arn' => $topicArn,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function publishToTopic($topicArn, $messageText)
    {
        try {
            $result = $this->sns->publish([
                'TopicArn' => $topicArn,
                'Message' => $messageText,
                'MessageAttributes' => [
                    'SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Transactional'
                    ]
                ]
            ]);

            Log::info('Message Published to Topic', [
                'topic_arn' => $topicArn,
                'message_id' => $result['MessageId'],
                'message_preview' => substr($messageText, 0, 50)
            ]);

            return [
                'success' => true,
                'message_id' => $result['MessageId'],
                'sent_at' => now(),
                'provider' => 'aws-sns-topic'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to publish to topic', [
                'topic_arn' => $topicArn,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If number starts with 0, remove it
        $number = ltrim($number, '0');
        
        // Add India country code (+91) if not present
        if (strlen($number) === 10) {
            $number = '+91' . $number;
        } else {
            // If number doesn't have country code, add it
            if (strpos($number, '91') !== 0) {
                $number = '+91' . $number;
            } else {
                $number = '+' . $number;
            }
        }
        
        return $number;
    }

    protected function formatMessage($template, $variables)
    {
        $messageText = $template->content;
        foreach ($variables as $key => $value) {
            $messageText = str_replace('{' . $key . '}', $value, $messageText);
        }
        return $messageText;
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
