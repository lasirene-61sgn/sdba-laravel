<?php

namespace App\Services;

use App\Models\Customer;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Log;

class RealTimeNotificationService
{
    private function getAccessToken()
    {
        // This looks for storage/app/firebase-auth.json based on your .env
        $path = base_path(env('FIREBASE_CREDENTIALS', 'storage/app/firebase-auth.json'));

        if (!file_exists($path)) {
            Log::error("Firebase credentials file not found at: " . $path);
            return null;
        }

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        try {
            $credentials = new ServiceAccountCredentials($scopes, $path);
            $token = $credentials->fetchAuthToken();
            return $token['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error("Identity Check Failed: " . $e->getMessage());
            return null;
        }
    }

    public function sendRealTimeNotification($customerId, $type, $messageBody, $extraData = [])
    {
        // Get the customer by ID
        $customer = Customer::find($customerId);
        
        if (!$customer) {
            return ['success' => false, 'error' => 'Customer not found'];
        }
        
        // Check if customer has an FCM token
        if (!$customer->fcm_token) {
            return ['success' => false, 'error' => 'Customer does not have an FCM token'];
        }
        
        $projectId = "jalore-d7dc0"; // Your Project ID from JSON
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return ['success' => false, 'error' => 'Could not generate Google Access Token'];
        }
        
        $title = $this->getNotificationTitle($type);
        
        // Convert all extraData values to strings as FCM requires all data values to be strings
        $processedExtraData = [];
        foreach ($extraData as $key => $value) {
            $processedExtraData[$key] = is_scalar($value) ? (string)$value : json_encode($value);
        }
        
        $data = [
            "message" => [
                "token" => $customer->fcm_token,
                "notification" => [
                    "title" => $title,
                    "body" => $messageBody
                ],
                "data" => array_merge([
                    "type" => $type,
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                    "customer_id" => (string)$customerId
                ], $processedExtraData)
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $accessToken,
                "Content-Type: application/json"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['success' => false, 'error' => $error];
        }
        
        if ($httpCode === 200) {
            return ['success' => true, 'message' => 'Notification sent successfully'];
        } else {
            return ['success' => false, 'error' => 'HTTP Error: ' . $httpCode . ', Response: ' . $response];
        }
    }

    public function sendBatchNotification($type, $messageBody, $extraData = [])
    {
        // Get all tokens from your Customers table
        $tokens = Customer::whereNotNull('fcm_token')->distinct()->pluck('fcm_token')->toArray();
        
        if (empty($tokens)) {
            return ['success' => false, 'error' => 'No customer tokens found'];
        }

        $projectId = "sirohiapp-c29cf"; // Your Project ID from JSON
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return ['success' => false, 'error' => 'Could not generate Google Access Token'];
        }

        $title = $this->getNotificationTitle($type);
        $successCount = 0;
        $errorCount = 0;

        // Process in batches of 100 for better performance
        foreach (array_chunk($tokens, 100) as $tokenBatch) {
            $mh = curl_multi_init();
            $handles = [];

            foreach ($tokenBatch as $token) {
                // Convert all extraData values to strings as FCM requires all data values to be strings
                $processedExtraData = [];
                foreach ($extraData as $key => $value) {
                    $processedExtraData[$key] = is_scalar($value) ? (string)$value : json_encode($value);
                }
                
                $ch = curl_init($url);
                $data = [
                    "message" => [
                        "token" => $token,
                        "notification" => [
                            "title" => $title,
                            "body" => $messageBody
                        ],
                        "data" => array_merge([
                            "type" => $type,
                            "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                        ], $processedExtraData)
                    ]
                ];

                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Bearer " . $accessToken,
                        "Content-Type: application/json"
                    ],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_SSL_VERIFYPEER => false
                ]);

                curl_multi_add_handle($mh, $ch);
                $handles[] = $ch;
            }

            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running > 0);

            foreach ($handles as $ch) {
                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($status === 200) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }
            curl_multi_close($mh);
        }

        return [
            'success' => true,
            'sent_count' => $successCount,
            'failed_count' => $errorCount
        ];
    }

    private function getNotificationTitle($type)
    {
        $titles = [
            'event' => 'New Event Added',
            'news' => 'New News Added',
            'gallery' => 'New Gallery Added',
            'birthday' => 'Today Birthday',
            'anniversary' => 'Today Anniversary',
        ];
        return $titles[$type] ?? 'New Notification';
    }
}