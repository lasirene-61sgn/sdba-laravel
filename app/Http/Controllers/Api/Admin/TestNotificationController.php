<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Services\NotificationsService;
use App\Services\RealTimeNotificationService;

class TestNotificationController extends Controller
{
    public function sendTestNotificationToAllCustomers(
        Request $request, 
        NotificationsService $notificationService,
        RealTimeNotificationService $realTimeNotificationService
    )
    {
        $adminId = $request->user()->id;
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'type' => 'required|in:event,news,gallery,custom'
        ]);
        
        // Get all customers of the admin who have FCM tokens
        $customers = Customer::where('admin_id', $adminId)
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->get();
        
        $successfulNotifications = 0;
        $failedNotifications = 0;
        $results = [];
        
        foreach ($customers as $customer) {
            $result = $realTimeNotificationService->sendRealTimeNotification(
                $customer->id,
                $request->type,
                $request->message,
                [
                    'title' => $request->title,
                    'admin_id' => $adminId,
                    'timestamp' => now()->toISOString()
                ]
            );
            
            if ($result['success']) {
                $successfulNotifications++;
            } else {
                $failedNotifications++;
            }
            
            $results[] = [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'result' => $result
            ];
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Test notification sent to all customers',
            'data' => [
                'total_customers' => $customers->count(),
                'successful_notifications' => $successfulNotifications,
                'failed_notifications' => $failedNotifications,
                'results' => $results
            ]
        ]);
    }
}