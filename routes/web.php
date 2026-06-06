<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CommitteePersonController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GalleryItemController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\SupportCategoryController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\SupportTypeController;
use App\Http\Controllers\Customer\EventRSVPController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\SuperAdminAuthController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\AdminManagementController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\VillageController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerPlanController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Customer\CustomerAuthController;
use App\Http\Controllers\Customer\CustomerController as CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerPollController;
use App\Http\Controllers\Customer\FamilyMemberController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\HelplineController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');
// Super Admin Authentication Routes
Route::prefix('superadmin')->group(function () {
    Route::get('/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('superadmin.login');
    Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('superadmin.login.post');
    Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// Customer Authentication Routes
Route::prefix('customer')->group(function () {
    // Login with mobile and OTP
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/send-otp', [CustomerAuthController::class, 'sendOTP'])->name('customer.send.otp');
    Route::get('/verify-otp', [CustomerAuthController::class, 'showOTPForm'])->name('customer.verify.otp.form');
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOTP'])->name('customer.verify.otp');

    // Set password for first time
    Route::get('set-password/{customer}', [CustomerAuthController::class, 'showSetPasswordForm'])->name('customer.set.password.form');
    Route::post('set-password', [CustomerAuthController::class, 'setPassword'])->name('customer.set.password');

    // Login with password
    Route::get('password-login', [CustomerAuthController::class, 'showPasswordLoginForm'])->name('customer.password.login');
    Route::post('password-login', [CustomerAuthController::class, 'loginWithPassword'])->name('customer.password.login.post');

    // Forgot password
    Route::get('forgot-password', [CustomerAuthController::class, 'showForgotPasswordForm'])->name('customer.forgot.password');
    Route::post('forgot-password/send-otp', [CustomerAuthController::class, 'sendForgotPasswordOTP'])->name('customer.forgot.password.otp');
    Route::get('reset-password', [CustomerAuthController::class, 'showResetPasswordForm'])->name('customer.reset.password.form');
    Route::post('reset-password', [CustomerAuthController::class, 'resetPassword'])->name('customer.reset.password');

    // --- Protected Routes (Requires 'customer' Guard) ---
    Route::middleware(['auth:customer'])->group(function () {

        // Dashboard
        Route::get('dashboard', [CustomerDashboardController::class, 'dashboard'])->name('customer.dashboard');

        // Profile routes
        Route::get('profile', [CustomerDashboardController::class, 'profile'])->name('customer.profile');
        Route::get('profile/edit', [CustomerDashboardController::class, 'editProfile'])->name('customer.edit.profile');
        Route::put('profile', [CustomerDashboardController::class, 'updateProfile'])->name('customer.update.profile');

        // Family members routes
        Route::prefix('family-members')->group(function () {
            Route::get('/', [FamilyMemberController::class, 'index'])->name('customer.family.members.index');
            Route::get('/create', [FamilyMemberController::class, 'create'])->name('customer.family.members.create');
            Route::post('/', [FamilyMemberController::class, 'store'])->name('customer.family.members.store');
            Route::get('/{familyMember}', [FamilyMemberController::class, 'show'])->name('customer.family.members.show');
            Route::get('/{familyMember}/edit', [FamilyMemberController::class, 'edit'])->name('customer.family.members.edit');
            Route::put('/{familyMember}', [FamilyMemberController::class, 'update'])->name('customer.family.members.update');
            Route::delete('/{familyMember}', [FamilyMemberController::class, 'destroy'])->name('customer.family.members.destroy');
        });

        // Plans routes
        Route::get('plans', [CustomerDashboardController::class, 'plans'])->name('customer.plans');

        // Customer list route
        Route::get('customers', [CustomerDashboardController::class, 'listCustomers'])->name('customer.list');

        // Customer detail route
        Route::get('customers/{id}', [CustomerDashboardController::class, 'showCustomer'])->name('customer.show');

        // About Us route
        Route::get('about-us', [CustomerDashboardController::class, 'aboutUs'])->name('customer.about-us');

        // Gallery route - show gallery items from the customer's admin
        Route::get('gallery', [CustomerDashboardController::class, 'gallery'])->name('customer.gallery');
        Route::get('gallery/{id}', [CustomerDashboardController::class, 'showGalleryItem'])->name('customer.gallery.show');

        // Banner route - show banner items from the customer's admin
        Route::get('banner', [CustomerDashboardController::class, 'banner'])->name('customer.banner');

        // Notice route - show notice items from the customer's admin
        Route::get('notice', [CustomerDashboardController::class, 'notice'])->name('customer.notice');
        Route::get('notice/{id}', [CustomerDashboardController::class, 'showNoticeItem'])->name('customer.notice.show');

        // Village route - show village items from the customer's admin
        Route::get('village', [CustomerDashboardController::class, 'village'])->name('customer.village');

        // Event route - show event items from the customer's admin
        Route::get('event', [CustomerDashboardController::class, 'event'])->name('customer.event');
        Route::post('event/{eventId}/rsvp', [EventRSVPController::class, 'rsvp'])->name('customer.event.rsvp');
        Route::get('event/qr-attend/{eventId}', [EventRSVPController::class, 'qrAttend'])->name('customer.event.qr-attend');

        // News route - show news items from the customer's admin
        Route::get('news', [CustomerDashboardController::class, 'news'])->name('customer.news');

        // Support route - show support items from the customer's admin
        Route::get('support', [CustomerDashboardController::class, 'support'])->name('customer.support');
        Route::get('support/{id}', [CustomerDashboardController::class, 'showSupportItem'])->name('customer.support.show');

        // Committee route - show committee items from the customer's admin
        Route::get('committee', [CustomerDashboardController::class, 'committee'])->name('customer.committee');

        // Customer plan route - show customer plan items from the customer's admin
        Route::get('customer-plan', [CustomerDashboardController::class, 'customerPlan'])->name('customer.customer_plan');

        // Polls route
        Route::get('polls', [CustomerPollController::class, 'index'])->name('customer.polls');
        Route::post('polls/{poll}/vote', [CustomerPollController::class, 'vote'])->name('customer.polls.vote');

        // Logout
        Route::post('logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
    });
});

// --- Protected Routes ---

// Super Admin Routes
Route::prefix('superadmin')->middleware(['auth:superadmin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('superadmin.dashboard');
    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\SuperAdmin\SuperAdminAuthController::class, 'showProfile'])->name('superadmin.profile');
    Route::put('/profile', [App\Http\Controllers\SuperAdmin\SuperAdminAuthController::class, 'updateProfile'])->name('superadmin.profile.update');
    // Admin Management Routes
    Route::get('/admins', [AdminManagementController::class, 'index'])->name('superadmin.admins.index');
    Route::get('/admins/create', [AdminManagementController::class, 'create'])->name('superadmin.admins.create');
    Route::post('/admins', [AdminManagementController::class, 'store'])->name('superadmin.admins.store');
    Route::get('/admins/{admin}/edit', [AdminManagementController::class, 'edit'])->name('superadmin.admins.edit');
    Route::put('/admins/{admin}', [AdminManagementController::class, 'update'])->name('superadmin.admins.update');
    Route::delete('/admins/{admin}', [AdminManagementController::class, 'destroy'])->name('superadmin.admins.destroy');
});

// Admin Routes
Route::prefix('admin')->middleware(['committee.member.auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    //Gallery Routes
    Route::resource('gallery', GalleryItemController::class)->names('admin.gallery')->parameters(['gallery' => 'galleryItem']);

    Route::post('gallery/bulk-delete', [GalleryItemController::class, 'bulkDelete'])->name('admin.gallery.bulk-delete');
    // Route::resource('gallery', GalleryItemController::class);

    //Banner Routes
    Route::resource('banner', BannerController::class)->names('admin.banner');

    //Notice Routes
    Route::resource('notice', NoticeController::class)->names('admin.notice');
    // Village Management Routes
    Route::resource('villages', VillageController::class)->names('admin.village');
    //Event Routes
    Route::resource('event', EventController::class)->names('admin.event');
    Route::get('event/{event}/rsvp-details', [EventController::class, 'rsvpDetails'])->name('admin.event.rsvp-details');
    Route::get('event/{event}/attendance', [EventController::class, 'attendance'])->name('admin.event.attendance');
    Route::get('event/{event}/rsvp-reports', [EventController::class, 'rsvpReports'])->name('admin.event.rsvp-reports');

    //News Routes
    Route::resource('news', NewsController::class)->names('admin.news');
    //Support Routes
    Route::resource('supports', SupportController::class)->names('admin.supports');
    Route::post('support-types', [SupportTypeController::class, 'store'])->name('admin.support_types.store');

    Route::post('support-categories', [SupportCategoryController::class, 'store'])->name('admin.support_categories.store');

    Route::get('customers/bulk-upload', [CustomerController::class, 'showBulkUploadForm'])->name('admin.customers.bulk-upload-form');
    Route::post('customers/bulk-upload', [CustomerController::class, 'bulkUpload'])->name('admin.customers.bulk-upload');
    // Route to view the dedicated split page
    Route::get('/customer/scan-create', [CustomerController::class, 'scanCreateForm'])->name('admin.customer.scan-create-form');

    // Route handling the backend Gemini processing
    Route::post('/customer/scan-card', [CustomerController::class, 'scanCard'])->name('admin.customer.scan-card');


    // Explicitly add 'admin.' to the name chain so it matches your Blade files!
Route::prefix('customer/{customer}/family')->name('admin.customer.family.')->group(function () {
    Route::get('/', [CustomerController::class, 'indexFamilyMember'])->name('index');          // admin.customer.family.index
    Route::get('/create', [CustomerController::class, 'createFamilyMember'])->name('create');  // admin.customer.family.create
    Route::post('/', [CustomerController::class, 'storeFamilyMember'])->name('store');         // admin.customer.family.store
    Route::get('/{familyMemberId}/edit', [CustomerController::class, 'editFamilyMember'])->name('edit'); // admin.customer.family.edit
    Route::put('/{familyMemberId}', [CustomerController::class, 'updateFamilyMember'])->name('update');   // admin.customer.family.update
    Route::delete('/{familyMemberId}', [CustomerController::class, 'deleteFamilyMember'])->name('destroy'); // admin.customer.family.destroy
});

    //Bill Routes
    Route::resource('bills', BillController::class)->names('admin.bills');
    Route::get('bills/customer/{customerId}/details', [BillController::class, 'getCustomerDetails'])->name('admin.bills.customer.details');

    //Committe People
    Route::get('committee/bulk-upload', [CommitteePersonController::class, 'showBulkUploadForm'])->name('committee.bulk_upload_form');
    Route::post('committee/bulk-upload', [CommitteePersonController::class, 'bulkUpload'])->name('committee.bulk_upload');
    Route::resource('committee', CommitteePersonController::class)->names('admin.committee')->parameters(['committee' => 'committeePerson']);
    // Customer Management Routes
    Route::resource('customers', CustomerController::class)->names('admin.customers');

    // Customer Plan Management Routes
    Route::resource('customer', CustomerController::class)->names('admin.customer');
    Route::resource('customer-plan', CustomerPlanController::class)->except(['show'])->names('admin.customer-plan')->parameters(['customer-plan' => 'customerPlan']);
    Route::get('/customer-plans/get-customer-details/{customerId}', [CustomerPlanController::class, 'getCustomerDetails'])->name('admin.customer-plans.get-customer-details');

    // About Us Routes
    Route::get('/about-us', [AboutUsController::class, 'index'])->name('admin.about-us.index');
    Route::put('/about-us', [AboutUsController::class, 'update'])->name('admin.about-us.update');

    // Helpline Section
    Route::resource('helpline', HelplineController::class)->names('admin.helpline');

    // Polls Routes
    Route::resource('polls', PollController::class)->names('admin.polls');
    Route::post('polls/{poll}/toggle-active', [PollController::class, 'toggleActive'])->name('admin.polls.toggle-active');
});

// Home route
Route::get('/', function () {
    return redirect()->route('customer.login');
});

// Debug route to check customer count
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

Route::get('/debug/customers', function () {
    try {
        $count = DB::table('customers')->count();
        return response()->json(['status' => 'success', 'count' => $count]);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
});

// Debug route to create a test customer
Route::get('/debug/create-customer', function () {
    try {
        $customer = new \App\Models\Customer();
        $customer->name = 'Test Customer';
        $customer->mobile = '1234567890';
        $customer->password = Hash::make('password');
        $customer->is_password_set = true;
        $customer->save();

        return response()->json(['status' => 'success', 'message' => 'Test customer created']);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
});

// Test QR Code route
Route::get('/test/qr-code', [App\Http\Controllers\TestQrCodeController::class, 'test']);
Route::get('/test/event-qr-code', [App\Http\Controllers\TestQrCodeController::class, 'testEventQrCode']);
Route::get('/test/event-qr-code-json', [App\Http\Controllers\TestQrCodeController::class, 'testEventQrCodeJson']);
Route::get('/test/qr-code-scanning/{eventId?}', [App\Http\Controllers\TestQrCodeController::class, 'testQrCodeScanning']);

// Debug route to test QR code URL generation
Route::get('/debug/qr-url/{eventId}', function ($eventId) {
    try {
        $url = route('customer.event.qr-attend', ['eventId' => $eventId]);
        return response()->json([
            'status' => 'success',
            'event_id' => $eventId,
            'generated_url' => $url,
            'window_location_origin' => url('/'),
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
// Temporary WhatsApp Test Route
Route::get('/test-whatsapp/{mobile}', function ($mobile) {
    $service = new \App\Services\WhatsAppOTPService();
    $otp = "123456"; // Test OTP
    $result = $service->sendOTP($mobile, $otp);

    return response()->json($result);
});
