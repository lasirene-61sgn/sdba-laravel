<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RealTimeNotificationService;
use App\Models\Customer;

class TestRealTimeNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:real-time-notifications {--customer=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test real-time push notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing real-time push notifications...');

        // Get customer ID from option or prompt
        $customerId = $this->option('customer') ?? $this->ask('Enter customer ID to send notification to');

        // Get the customer
        $customer = Customer::find($customerId);
        if (!$customer) {
            $this->error("Customer with ID {$customerId} not found!");
            return 1;
        }

        // Create the real-time notification service
        $notificationService = new RealTimeNotificationService();

        // Send a test notification
        $result = $notificationService->sendRealTimeNotification(
            $customer->id,
            'test_notification',
            'This is a test real-time notification!',
            [
                'test_data' => 'This is sample test data',
                'timestamp' => now()->toISOString()
            ]
        );

        if ($result) {
            $this->info("Real-time notification sent successfully to customer {$customer->name} (ID: {$customer->id})");
            $this->info("Notification ID: {$result->id}");
        } else {
            $this->error("Failed to send real-time notification!");
            return 1;
        }

        return 0;
    }
}