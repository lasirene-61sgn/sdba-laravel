<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RealTimeNotificationService;

class NotificationApiController extends Controller
{
    protected $notificationService;

    public function __construct(RealTimeNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function broadcastNotification(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'message' => 'required|string',
        ]);

        $result = $this->notificationService->sendBatchNotification(
            $request->type, 
            $request->message,
            $request->extra_data ?? []
        );

        return response()->json($result);
    }
}