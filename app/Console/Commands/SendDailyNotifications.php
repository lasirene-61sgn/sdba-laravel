<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\NotificationsService;

class SendDailyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily notifications for birthdays and anniversaries';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Sending daily notifications...');
        
        // Create notifications for today's birthdays
        $notificationService = new NotificationsService();
        $notificationService->createTodaysBirthdaysNotifications();
        $this->info('Birthday notifications sent.');
        
        // Create notifications for today's anniversaries
        $notificationService->createTodaysAnniversariesNotifications();
        $this->info('Anniversary notifications sent.');
        
        $this->info('Daily notifications sent successfully.');

        return 0;
    }
}
