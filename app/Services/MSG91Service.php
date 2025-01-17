<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class MSG91Service
{
    protected $authKey;
    protected $baseUrl;
    protected $defaultTemplateId;
    protected $senderId;

    public function __construct()
    {
        $this->authKey = Config::get('services.msg91.auth_key');
        $this->baseUrl = Config::get('services.msg91.base_url', 'https://control.msg91.com/api/v5/');
        $this->defaultTemplateId = Config::get('services.msg91.template_id');
        $this->senderId = Config::get('services.msg91.sender_id');

        if (empty($this->authKey)) {
            throw new \Exception('MSG91 auth key is not configured');
        }
    }

    public function sendMessage($mobile, $message, $variables = [])
    {
        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'content-type' => 'application/json'
            ])->post($this->baseUrl . 'flow/', [
                'template_id' => $this->defaultTemplateId,
                'recipients' => [
                    [
                        'mobiles' => $mobile,
                        'VAR1' => $message,
                        ...$variables
                    ]
                ]
            ]);

            Log::info('MSG91 API Response', [
                'response' => $response->json(),
                'mobile' => $mobile
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('MSG91 API Error', [
                'error' => $e->getMessage(),
                'mobile' => $mobile
            ]);
            throw $e;
        }
    }

    public function sendBulkMessage($recipients, $templateId = null)
    {
        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'content-type' => 'application/json'
            ])->post($this->baseUrl . 'flow/', [
                'template_id' => $templateId ?? $this->defaultTemplateId,
                'recipients' => $recipients
            ]);

            Log::info('MSG91 Bulk API Response', [
                'response' => $response->json(),
                'recipients_count' => count($recipients)
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('MSG91 Bulk API Error', [
                'error' => $e->getMessage(),
                'recipients_count' => count($recipients)
            ]);
            throw $e;
        }
    }
}
