<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\GalleryItem;
use App\Models\Event;
use Carbon\Carbon;
use App\Services\RealTimeNotificationService;

class NotificationsService
{
    protected $realTimeNotificationService;

    public function __construct(RealTimeNotificationService $realTimeNotificationService = null)
    {
        $this->realTimeNotificationService = $realTimeNotificationService;
    }

    /**
     * Create notification for a new gallery item
     */
    public function createGalleryAddedNotification($adminId, $galleryItem)
    {
        // Get all customers of the admin
        $customers = Customer::where('admin_id', $adminId)->get();
        
        foreach ($customers as $customer) {
            Notification::create([
                'customer_id' => $customer->id,
                'type' => 'gallery_added',
                'message' => 'New gallery item added: ' . $galleryItem->name,
                'related_id' => $galleryItem->id,
                'related_type' => 'gallery',
            ]);

            // Send real-time notification if service is available
            if ($this->realTimeNotificationService) {
                $this->realTimeNotificationService->sendRealTimeNotification(
                    $customer->id,
                    'gallery_added',
                    'New gallery item added: ' . $galleryItem->name,
                    [
                        'gallery_id' => $galleryItem->id,
                        'gallery_name' => $galleryItem->name
                    ]
                );
            }
        }
    }
    
    /**
     * Create notification for a new event
     */
    public function createEventAddedNotification($adminId, $event)
    {
        // Get all customers of the admin
        $customers = Customer::where('admin_id', $adminId)->get();
        
        foreach ($customers as $customer) {
            Notification::create([
                'customer_id' => $customer->id,
                'type' => 'event_added',
                'message' => 'New event added: ' . $event->name,
                'related_id' => $event->id,
                'related_type' => 'event',
            ]);

            // Send real-time notification if service is available
            if ($this->realTimeNotificationService) {
                $this->realTimeNotificationService->sendRealTimeNotification(
                    $customer->id,
                    'event_added',
                    'New event added: ' . $event->name,
                    [
                        'event_id' => $event->id,
                        'event_name' => $event->name,
                        'event_date' => $event->event_date ?? null
                    ]
                );
            }
        }
    }
    
    /**
     * Create notification for a new banner
     */
    public function createBannerAddedNotification($adminId, $banner)
    {
        // Get all customers of the admin
        $customers = Customer::where('admin_id', $adminId)->get();
        
        foreach ($customers as $customer) {
            Notification::create([
                'customer_id' => $customer->id,
                'type' => 'banner_added',
                'message' => 'New banner added: ' . ($banner->title ?? 'Banner'),
                'related_id' => $banner->id,
                'related_type' => 'banner',
            ]);

            // Send real-time notification if service is available
            if ($this->realTimeNotificationService) {
                $this->realTimeNotificationService->sendRealTimeNotification(
                    $customer->id,
                    'banner_added',
                    'New banner added: ' . ($banner->title ?? 'Banner'),
                    [
                        'banner_id' => $banner->id,
                        'banner_title' => $banner->title
                    ]
                );
            }
        }
    }
    
    /**
     * Create notification for a new news
     */
    public function createNewsAddedNotification($adminId, $news)
    {
        // Get all customers of the admin
        $customers = Customer::where('admin_id', $adminId)->get();
        
        foreach ($customers as $customer) {
            Notification::create([
                'customer_id' => $customer->id,
                'type' => 'news_added',
                'message' => 'New news added: ' . $news->title,
                'related_id' => $news->id,
                'related_type' => 'news',
            ]);

            // Send real-time notification if service is available
            if ($this->realTimeNotificationService) {
                $this->realTimeNotificationService->sendRealTimeNotification(
                    $customer->id,
                    'news_added',
                    'New news added: ' . $news->title,
                    [
                        'news_id' => $news->id,
                        'news_title' => $news->title
                    ]
                );
            }
        }
    }
    
    /**
     * Create notifications for today's birthdays
     */
    public function createTodaysBirthdaysNotifications()
    {
        // Get all customers with today's birthday
        $todayBirthdays = Customer::whereNotNull('date_of_birth')
            ->whereRaw('MONTH(date_of_birth) = ?', [Carbon::now()->month])
            ->whereRaw('DAY(date_of_birth) = ?', [Carbon::now()->day])
            ->get();
        
        foreach ($todayBirthdays as $customer) {
            // Create notification for the customer themselves
            Notification::create([
                'customer_id' => $customer->id,
                'type' => 'birthday_today',
                'message' => 'Today is your birthday! Happy Birthday!',
                'related_id' => $customer->id,
                'related_type' => 'customer',
            ]);

            // Send real-time notification if service is available
            if ($this->realTimeNotificationService) {
                $this->realTimeNotificationService->sendRealTimeNotification(
                    $customer->id,
                    'birthday_today',
                    'Today is your birthday! Happy Birthday!',
                    [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name
                    ]
                );
            }
            
            // Create notifications for other customers of the same admin
            $otherCustomers = Customer::where('admin_id', $customer->admin_id)
                ->where('id', '!=', $customer->id)
                ->select('id', 'name') // Only select required fields to avoid loading unnecessary data
                ->get();
                
            foreach ($otherCustomers as $otherCustomer) {
                Notification::create([
                    'customer_id' => $otherCustomer->id,
                    'type' => 'birthday_today',
                    'message' => $customer->name . ' has a birthday today!',
                    'related_id' => $customer->id,
                    'related_type' => 'customer',
                ]);

                // Send real-time notification if service is available
                if ($this->realTimeNotificationService) {
                    $this->realTimeNotificationService->sendRealTimeNotification(
                        $otherCustomer->id,
                        'birthday_today',
                        $customer->name . ' has a birthday today!',
                        [
                            'customer_id' => $customer->id,
                            'customer_name' => $customer->name
                        ]
                    );
                }
            }
        }
    }
    
    /**
     * Create notifications for today's anniversaries
     */
    public function createTodaysAnniversariesNotifications()
    {
        // Get all customers with today's anniversary
        $todayAnniversaries = Customer::whereNotNull('anniversary_date')
            ->whereRaw('MONTH(anniversary_date) = ?', [Carbon::now()->month])
            ->whereRaw('DAY(anniversary_date) = ?', [Carbon::now()->day])
            ->get();
        
        foreach ($todayAnniversaries as $customer) {
            // Create notification for the customer themselves
            Notification::create([
                'customer_id' => $customer->id,
                'type' => 'anniversary_today',
                'message' => 'Today is your anniversary! Happy Anniversary!',
                'related_id' => $customer->id,
                'related_type' => 'customer',
            ]);

            // Send real-time notification if service is available
            if ($this->realTimeNotificationService) {
                $this->realTimeNotificationService->sendRealTimeNotification(
                    $customer->id,
                    'anniversary_today',
                    'Today is your anniversary! Happy Anniversary!',
                    [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name
                    ]
                );
            }
            
            // Create notifications for other customers of the same admin
            $otherCustomers = Customer::where('admin_id', $customer->admin_id)
                ->where('id', '!=', $customer->id)
                ->select('id', 'name') // Only select required fields to avoid loading unnecessary data
                ->get();
                
            foreach ($otherCustomers as $otherCustomer) {
                Notification::create([
                    'customer_id' => $otherCustomer->id,
                    'type' => 'anniversary_today',
                    'message' => $customer->name . ' has an anniversary today!',
                    'related_id' => $customer->id,
                    'related_type' => 'customer',
                ]);

                // Send real-time notification if service is available
                if ($this->realTimeNotificationService) {
                    $this->realTimeNotificationService->sendRealTimeNotification(
                        $otherCustomer->id,
                        'anniversary_today',
                        $customer->name . ' has an anniversary today!',
                        [
                            'customer_id' => $customer->id,
                            'customer_name' => $customer->name
                        ]
                    );
                }
            }
        }
    }
}