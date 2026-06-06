<?php

use App\Http\Controllers\Api\NotificationApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\VillageController;
use App\Http\Controllers\Api\Admin\SupportController;
use App\Http\Controllers\Api\Admin\SupportTypeController;
use App\Http\Controllers\Api\Admin\SupportCategoryController;
use App\Http\Controllers\Api\Admin\BannerController;
use App\Http\Controllers\Api\Admin\CustomerPlanController;
use App\Http\Controllers\Api\Admin\EventController;
use App\Http\Controllers\Api\Admin\GalleryItemController;
use App\Http\Controllers\Api\Admin\NewsController;
use App\Http\Controllers\Api\Admin\NoticeController;
use App\Http\Controllers\Api\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Api\Customer\CustomerController as CustomerApiController;
use App\Http\Controllers\Api\Customer\EventRSVPController;

// Admin API Routes
Route::prefix('admin')->group(function () {
    // Authentication Routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Protected Routes
    Route::middleware(['auth:sanctum'])->group(function () {
        // Admin Profile
        Route::get('/profile', [AdminController::class, 'profile']);

        // Customers
        Route::apiResource('customers', CustomerController::class);

        // Villages
        Route::apiResource('villages', VillageController::class);

        // Supports
        Route::apiResource('supports', SupportController::class);

        // Support Types
        Route::apiResource('support-types', SupportTypeController::class);

        // Support Categories
        Route::apiResource('support-categories', SupportCategoryController::class);

        // Banners
        Route::apiResource('banners', BannerController::class);

        // Customer Plans
        Route::apiResource('customer-plans', CustomerPlanController::class);

        // Events
        Route::apiResource('events', EventController::class);

        // Gallery Items
        Route::apiResource('gallery-items', GalleryItemController::class);

        // News
        Route::apiResource('news', NewsController::class);

        // Notices
        Route::apiResource('notices', NoticeController::class);
        
        // Test Notifications
        Route::post('/test-notifications/send-to-all', [\App\Http\Controllers\Api\Admin\TestNotificationController::class, 'sendTestNotificationToAllCustomers']);
    });
});

// Customer API Routes
Route::prefix('customer')->group(function () {
    // Authentication Routes
    Route::post('/send-otp', [CustomerAuthController::class, 'sendOTP']);
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOTP']);
    Route::post('/set-password', [CustomerAuthController::class, 'setPassword']);
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::post('/forgot-password-otp', [CustomerAuthController::class, 'sendForgotPasswordOTP']);
    Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword']);

    // Protected Routes
    Route::middleware(['auth:sanctum'])->group(function () {
        // Customer Profile
        Route::get('/profile', [CustomerApiController::class, 'profile']);
        Route::put('/profile', [CustomerApiController::class, 'updateProfile']);

        // Customer Plans
        Route::get('/plans', [CustomerApiController::class, 'plans']);

        // Customer List
        Route::get('/states', [CustomerApiController::class, 'listStates']);
    Route::get('/cities', [CustomerApiController::class, 'listCities']);
        Route::get('/customers', [CustomerApiController::class, 'listCustomers']);

        // Customer Detail
        Route::get('/customers/{id}', [CustomerApiController::class, 'showCustomer']);

        // Gallery
        Route::get('/gallery', [CustomerApiController::class, 'gallery']);

        // Banner
        Route::get('/banner', [CustomerApiController::class, 'banner']);

        // Notice
        Route::get('/notice', [CustomerApiController::class, 'notice']);

        // Village
        Route::get('/village', [CustomerApiController::class, 'village']);

        // Event
        Route::get('/event', [CustomerApiController::class, 'event']);
        Route::post('/event/{eventId}/rsvp', [EventRSVPController::class, 'rsvp']);
        Route::get('/event/{eventId}/rsvp', [EventRSVPController::class, 'getRsvpStatus']);

        // Event Response (Accept/Reject)
        Route::post('/event/{eventId}/respond', [CustomerApiController::class, 'respondToEvent']);
        Route::get('/event/{eventId}/response', [CustomerApiController::class, 'getEventResponse']);

        // News
        Route::get('/news', [CustomerApiController::class, 'news']);

        // Support
        Route::get('/support', [CustomerApiController::class, 'support']);

        // Committee
        Route::get('/committee', [CustomerApiController::class, 'committee']);

        // Customer Plan
        Route::get('/customer-plan', [CustomerApiController::class, 'customerPlan']);

        // About Us
        Route::get('/about-us', [CustomerApiController::class, 'aboutUs']);

        // Gallery Item Detail
        Route::get('/gallery/{id}', [CustomerApiController::class, 'showGalleryItem']);

        // Notice Item Detail
        Route::get('/notice/{id}', [CustomerApiController::class, 'showNoticeItem']);

        // Support Item Detail
        Route::get('/support/{id}', [CustomerApiController::class, 'showSupportItem']);

        // Customer Plan Detail
        Route::get('/customer-plan/{id}', [CustomerApiController::class, 'showCustomerPlan']);

        // Family Members
        Route::get('/family-members', [CustomerApiController::class, 'listFamilyMembers']);
        Route::get('/family-members/{id}', [CustomerApiController::class, 'showFamilyMember']);
        Route::post('/family-members', [CustomerApiController::class, 'createFamilyMember']);
        Route::put('/family-members/{id}', [CustomerApiController::class, 'updateFamilyMember']);
        Route::delete('/family-members/{id}', [CustomerApiController::class, 'deleteFamilyMember']);

        // Polls
        Route::get('/polls', [CustomerApiController::class, 'listPolls']);
        Route::post('/polls/{pollId}/vote', [CustomerApiController::class, 'voteOnPoll']);

        // Birthdays and Anniversaries
        Route::get('/today-birthdays', [CustomerApiController::class, 'todayBirthdays']);
        Route::get('/today-anniversaries', [CustomerApiController::class, 'todayAnniversaries']);

        // Notifications
        Route::get('/all-notifications', [CustomerApiController::class, 'getAllNotifications']);
        Route::post('/notifications/{id}/read', [CustomerApiController::class, 'markRead']);
        Route::post('/notifications/mark-all-read', [CustomerApiController::class, 'markallreadnotifications']);
        Route::get('/notifications/unread-count', [CustomerApiController::class, 'unreadNotificationsCount']);
        Route::post('/update-device-token', [CustomerApiController::class, 'updateDeviceToken']);

        // Route to manually test if the push notification is working
        Route::post('/test-real-time', [CustomerApiController::class, 'testRealTimeNotification']);
        // Business Name APIs
        Route::get('/business-names/categories', [CustomerApiController::class, 'getBusinessCategories']);
        Route::get('/business-names', [CustomerApiController::class, 'getBusinessNames']);
        Route::post('/send-broadcast', [NotificationApiController::class, 'broadcastNotification']);
        
        // Matrimony API
        Route::get('/customers-matrimony', [CustomerApiController::class, 'getCustomersWithMatrimony']);
        
        // Helpline
        Route::get('/helpline', [CustomerApiController::class, 'helpline']);
        
        // Logout
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
    });
});
