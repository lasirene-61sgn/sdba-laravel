<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppOTPService
{
    protected $authKey;
    protected $templateName;
    protected $apiBaseUrl;
    protected $integratedNumber;

    public function __construct()
    {
        $this->authKey = config('msg91.auth_key');
        $this->templateName = config('msg91.template_name');
        $this->apiBaseUrl = rtrim(config('msg91.api_base_url', 'https://control.msg91.com'), '/');
        $this->integratedNumber = (string)config('msg91.integrated_number');
    }

    /**
     * Send OTP via WhatsApp using MSG91
     */
    public function sendOTP($mobile, $otp)
    {
        if (empty($this->authKey)) {
            return ['success' => false, 'message' => 'MSG91 Auth Key is missing.'];
        }

        $mobile = $this->formatMobileNumber($mobile);

        // The EXACT structure required for MSG91 templates with Body variables and URL Buttons
        $payload = [
            "integrated_number" => $this->integratedNumber,
            "content_type" => "template",
            "payload" => [
                "type" => "template",
                "template" => [
                    "name" => $this->templateName,
                    "language" => [
                        "code" => "en",
                        "policy" => "deterministic"
                    ],
                    "to_and_components" => [
                        [
                            "to" => [(string)$mobile],
                            "components" => [
                                // Component for the message body {{1}}
                                "body_1" => [
                                    "type" => "text",
                                    "value" => (string)$otp
                                ],
                                // Component for the URL Button at index 0
                                "button_1" => [
                                    "type" => "text",
                                    "sub_type" => "url", // MANDATORY: Fixes the sub_type error
                                    "index" => "0",      // MANDATORY: Fixes the missing index error
                                    "value" => (string)$otp
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])->post($this->apiBaseUrl . '/api/v5/whatsapp/whatsapp-outbound-message/bulk/', $payload);

            // Log details to storage/logs/laravel.log
            Log::info('WhatsApp OTP Attempt for ' . $mobile, [
                'payload_sent' => $payload,
                'api_response' => $response->body()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'OTP sent successfully',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'MSG91 API Error: ' . ($response->json()['errors'] ?? 'Structure mismatch'),
                'details' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Service Exception: ' . $e->getMessage()];
        }
    }

    public function generateOTP()
    {
        return (string)rand(100000, 999999);
    }

    public function formatMobileNumber($mobile)
    {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        if (strlen($mobile) === 10) {
            $mobile = '91' . $mobile;
        }
        return (string)$mobile;
    }
}