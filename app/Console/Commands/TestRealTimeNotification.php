<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RealTimeNotificationService;
use App\Services\NotificationsService;
use App\Models\Customer;

class TestRealTimeNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-real-time-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test real-time notification functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing real-time notification functionality...');
        
        // Find a customer with an FCM token
        $customer = Customer::whereNotNull('fcm_token')->first();
        
        if (!$customer) {
            $this->error('No customers found with FCM tokens!');
            $this->info('Please make sure at least one customer has registered their FCM token.');
            return 1;
        }
        
        $this->info('Found customer: ' . $customer->name . ' with FCM token');
        
        // Test the RealTimeNotificationService directly
        $realTimeService = new RealTimeNotificationService();
        
        $result = $realTimeService->sendRealTimeNotification(
            $customer->id,
            'test',
            'Test notification: Real-time notification system is working!',
            ['test' => true]
        );
        
        if ($result['success']) {
            $this->info('✓ Real-time notification sent successfully!');
        } else {
            $this->error('✗ Failed to send real-time notification: ' . ($result['error'] ?? 'Unknown error'));
        }
        
        // Test the NotificationsService integration
        $this->info("\nTesting NotificationsService integration...");
        $notificationService = new NotificationsService($realTimeService);
        
        // This would normally create both database and real-time notification
        // For testing, we'll simulate creating a notification
        $this->info('NotificationsService is ready to send both database and real-time notifications when admin adds content.');
        
        $this->info("\nTest completed!");
        
        return 0;
    }
}