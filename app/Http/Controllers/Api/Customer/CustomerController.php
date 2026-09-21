<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\GalleryItem;
use App\Models\CommitteePerson;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Notice;
use App\Models\Village;
use App\Models\Event;
use App\Models\News;
use App\Models\Support;
use App\Models\CustomerPlan;
use App\Models\FamilyMember;
use App\Models\Poll;
use App\Models\PollResponse;
use App\Models\EventRSVP;
use App\Models\Helpline;
use App\Models\Link;
use App\Models\SubCategory;
use App\Services\RealTimeNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CustomerController extends Controller
{
    private function getValidatedCustomer()
    {
        $customer = Auth::guard('sanctum')->user();
        if (!$customer || !$customer->admin_id) {
            return null;
        }
        return $customer;
    }
    /**
     * Display the customer profile
     */
    public function profile(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Load customer with village relationship if customer has a village_id
        if ($customer->village_id) {
            $customer = Customer::with([
                'village' => function ($query) {
                    $query->select('id', 'name');
                }
            ])->find($customer->id);

            // Add village_name attribute to the customer object for frontend use
            $customer->village_name = $customer->village ? $customer->village->name : null;
        }

        // Convert to array to safely modify the response data
        $responseData = $customer->toArray();

        // FIX 1 & 2: Separate into independent IF statements so BOTH execute
        if ($customer->image) {
            $responseData['image'] = (strpos($customer->image, 'uploads/') === 0)
                ? url($customer->image)
                : url('storage/' . $customer->image);
        } else {
            $responseData['image'] = null;
        }

        if ($customer->background_image) {
            $responseData['background_image'] = (strpos($customer->background_image, 'uploads/') === 0)
                ? url($customer->background_image)
                : url('storage/' . $customer->background_image); // Fixed comma typo to string concatenation
        } else {
            $responseData['background_image'] = null;
        }

        return response()->json([
            'status' => 'success',
            'data' => $responseData
        ]);
    }

    /**
     * Update the customer profile
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'father_name' => 'nullable|string|max:100',
            'gotra' => 'nullable|string|max:100',
            'label_name' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'ms_firm_name' => 'nullable|string|max:100',
            'dno' => 'nullable|string|max:50',
            'street_road' => 'nullable|string|max:150',
            'address2' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'subcategory_id' => 'nullable',
            'business_name' => 'nullable|string|max:100',
            'category_id' => 'nullable',
            'product_service' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:500',
            'education' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'anniversary_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/customers'), $imageName);
            $validatedData['image'] = 'uploads/customers/' . $imageName;
        }

        if ($request->hasFile('background_image')) {
            $background_image = $request->file('background_image');
            $imageName = time() . '.' . $background_image->extension();
            // FIX 3: Fixed folder path string structure to include missing slashes
            $background_image->move(public_path('uploads/customer_backgrounds'), $imageName);
            $validatedData['background_image'] = 'uploads/customer_backgrounds/' . $imageName;
        }

        // Handle custom category
        if ($request->filled('product_service')) {
            $category = Category::firstOrCreate(['name' => $request->product_service]);
            $validatedData['category_id'] = $category->id;
            $validatedData['product_service'] = $category->name;
        } elseif ($request->filled('category_id') && !is_numeric($request->category_id)) {
            $category = Category::firstOrCreate(['name' => $request->category_id]);
            $validatedData['category_id'] = $category->id;
            $validatedData['product_service'] = $category->name;
        } elseif ($request->filled('category_id')) {
            $category = Category::find($request->category_id);
            if ($category) {
                $validatedData['product_service'] = $category->name;
            }
        }

        // Handle custom subcategory
        if ($request->filled('business_type')) {
            $subcategory = SubCategory::firstOrCreate([
                'name' => $request->business_type,
                'category_id' => $validatedData['category_id'] ?? $customer->category_id
            ]);
            $validatedData['subcategory_id'] = $subcategory->id;
            $validatedData['business_type'] = $subcategory->name;
        } elseif ($request->filled('subcategory_id') && !is_numeric($request->subcategory_id)) {
            $subcategory = SubCategory::firstOrCreate([
                'name' => $request->subcategory_id,
                'category_id' => $validatedData['category_id'] ?? $customer->category_id
            ]);
            $validatedData['subcategory_id'] = $subcategory->id;
            $validatedData['business_type'] = $subcategory->name;
        } elseif ($request->filled('subcategory_id')) {
            $subcategory = SubCategory::find($request->subcategory_id);
            if ($subcategory) {
                $validatedData['business_type'] = $subcategory->name;
            }
        }

        $customer->update($validatedData);

        // Reload customer with village relationship if customer has a village_id
        if ($customer->village_id) {
            $customer = Customer::with([
                'village' => function ($query) {
                    $query->select('id', 'name');
                }
            ])->find($customer->id);

            // Add village_name attribute to the customer object for frontend use
            $customer->village_name = $customer->village ? $customer->village->name : null;
        }

        $responseData = $customer->toArray();

        // Standardize URL output for the immediate update response block
        if (!empty($customer->image)) {
            $responseData['image'] = (strpos($customer->image, 'uploads/') === 0) ? url($customer->image) : url('storage/' . $customer->image);
        }

        if (!empty($customer->background_image)) {
            $responseData['background_image'] = (strpos($customer->background_image, 'uploads/') === 0) ? url($customer->background_image) : url('storage/' . $customer->background_image);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully!',
            'data' => $responseData
        ]);
    }

    /**
     * Display customer notifications
     */
    public function notifications(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get notifications for the customer, ordered by newest first
        $notifications = $customer->notifications()->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }

    /**
     * Get all types of notifications including admin updates
     */
    public function getAllNotifications(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        $notifications = [];

        // Get database notifications
        $databaseNotifications = \App\Models\Notification::where('customer_id', $customer->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($databaseNotifications as $dbNotification) {
            $notificationItem = [
                'id' => $dbNotification->id,
                'type' => $dbNotification->type,
                'title' => $this->getNotificationTitle($dbNotification->type),
                'message' => $dbNotification->message,
                'is_read' => $dbNotification->is_read,
                'read_at' => $dbNotification->read_at,
                'created_at' => $dbNotification->created_at,
                'data' => null
            ];

            // Add related data based on notification type
            if ($dbNotification->related_type && $dbNotification->related_id) {
                switch ($dbNotification->related_type) {
                    case 'event':
                        $event = \App\Models\Event::find($dbNotification->related_id);
                        if ($event) {
                            $notificationItem['data'] = $event;
                        }
                        break;
                    case 'gallery':
                    case 'gallery_item':
                        $gallery = \App\Models\GalleryItem::find($dbNotification->related_id);
                        if ($gallery) {
                            $notificationItem['data'] = $gallery;
                        }
                        break;
                    case 'news':
                        $news = \App\Models\News::find($dbNotification->related_id);
                        if ($news) {
                            $notificationItem['data'] = $news;
                        }
                        break;
                    case 'banner':
                        $banner = \App\Models\Banner::find($dbNotification->related_id);
                        if ($banner) {
                            $notificationItem['data'] = $banner;
                        }
                        break;
                    case 'customer':
                        $customerData = \App\Models\Customer::find($dbNotification->related_id);
                        if ($customerData) {
                            $notificationItem['data'] = $customerData;
                        }
                        break;
                }
            }

            $notifications[] = $notificationItem;
        }

        // // Get admin events
        // $events = \App\Models\Event::where('admin_id', $customer->admin_id)
        //     ->where('status', 'active')
        //     ->where('created_at', '>=', now()->subDays(7)) // Last 7 days
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        // foreach ($events as $event) {
        //     $notifications[] = [
        //         'type' => 'event',
        //         'title' => 'New Event Added',
        //         'message' => $event->name,
        //         'data' => $event,
        //         'created_at' => $event->created_at
        //     ];
        // }

        // // Get admin gallery items
        // $galleries = \App\Models\GalleryItem::where('admin_id', $customer->admin_id)
        //     ->where('status', 'active')
        //     ->where('created_at', '>=', now()->subDays(7)) // Last 7 days
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        // foreach ($galleries as $gallery) {
        //     $notifications[] = [
        //         'type' => 'gallery',
        //         'title' => 'New Gallery Added',
        //         'message' => $gallery->title,
        //         'data' => $gallery,
        //         'created_at' => $gallery->created_at
        //     ];
        // }

        // // Get admin news
        // $news = \App\Models\News::where('admin_id', $customer->admin_id)
        //     ->where('status', 'active')
        //     ->where('created_at', '>=', now()->subDays(7)) // Last 7 days
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        // foreach ($news as $new) {
        //     $notifications[] = [
        //         'type' => 'news',
        //         'title' => 'New News Added',
        //         'message' => $new->title,
        //         'data' => $new,
        //         'created_at' => $new->created_at
        //     ];
        // }

        // // Get today's birthdays
        // $birthdays = \App\Models\Customer::where('admin_id', $customer->admin_id)
        //     ->whereNotNull('date_of_birth')
        //     ->whereRaw('MONTH(date_of_birth) = ?', [date('m')])
        //     ->whereRaw('DAY(date_of_birth) = ?', [date('d')])
        //     ->select('id', 'name', 'mobile', 'date_of_birth')
        //     ->get();

        // foreach ($birthdays as $birthday) {
        //     $notifications[] = [
        //         'type' => 'birthday',
        //         'title' => 'Today is Birthday',
        //         'message' => $birthday->name . ' has a birthday today!',
        //         'data' => $birthday,
        //         'created_at' => now()
        //     ];
        // }

        // Get today's anniversaries
        $anniversaries = \App\Models\Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('anniversary_date')
            ->whereRaw('MONTH(anniversary_date) = ?', [date('m')])
            ->whereRaw('DAY(anniversary_date) = ?', [date('d')])
            ->select('id', 'name', 'mobile', 'anniversary_date')
            ->get();

        foreach ($anniversaries as $anniversary) {
            $notifications[] = [
                'type' => 'anniversary',
                'title' => 'Today is Anniversary',
                'message' => $anniversary->name . ' has an anniversary today!',
                'data' => $anniversary,
                'created_at' => now()
            ];
        }

        // Sort all notifications by date (newest first)
        usort($notifications, function ($a, $b) {
            $dateA = $a['created_at'] instanceof \Carbon\Carbon ? $a['created_at'] : strtotime($a['created_at']);
            $dateB = $b['created_at'] instanceof \Carbon\Carbon ? $b['created_at'] : strtotime($b['created_at']);

            if (is_numeric($dateA) && is_numeric($dateB)) {
                return $dateB - $dateA; // Descending order (newest first)
            }

            // If one is Carbon and other is string, convert both to timestamp
            $timestampA = is_object($dateA) ? $dateA->timestamp : $dateA;
            $timestampB = is_object($dateB) ? $dateB->timestamp : $dateB;

            return $timestampB - $timestampA;
        });

        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }

    /**
     * Helper method to get notification title based on type
     */
    private function getNotificationTitle($type)
    {
        $titles = [
            'event_added' => 'New Event Added',
            'news_added' => 'New News Added',
            'gallery_added' => 'New Gallery Added',
            'banner_added' => 'New Banner Added',
            'birthday_today' => 'Today is Birthday',
            'anniversary_today' => 'Today is Anniversary',
        ];

        return $titles[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * Display customer plans
     */
    public function plans(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();
        $plans = $customer->customerPlans()->with('customer')->get();

        return response()->json([
            'status' => 'success',
            'data' => $plans
        ]);
    }

    public function listStates(Request $request)
    {
        $customer = $this->getValidatedCustomer();
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Invalid customer data.'], 400);
        }

        $search = $request->query('search');

        // Group by state and count customers
        $query = Customer::select('state')
            ->selectRaw('count(*) as total_customers')
            ->where('admin_id', $customer->admin_id)
            ->whereNotNull('state')
            ->where('state', '!=', '');

        if ($search) {
            $query->where('state', 'LIKE', '%' . $search . '%');
        }

        $states = $query->groupBy('state')->get();

        return response()->json([
            'status' => 'success',
            'count' => $states->count(),
            'data' => $states
        ]);
    }

    /**
     * API 2: List Cities within a specific State with Customer Counts
     */
    public function listCities(Request $request)
    {
        $customer = $this->getValidatedCustomer();
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Invalid customer data.'], 400);
        }

        $state = $request->query('state');
        $search = $request->query('search');

        // We filter by admin context
        $query = Customer::select('city')
            ->selectRaw('count(*) as total_customers')
            ->where('admin_id', $customer->admin_id)
            ->whereNotNull('city')
            ->where('city', '!=', '');

        // Filter by State if provided in query params
        if ($state) {
            $query->where('state', $state);
        }

        // Search filter specifically for city name
        if ($search) {
            $query->where('city', 'LIKE', '%' . $search . '%');
        }

        $cities = $query->groupBy('city')->get();

        return response()->json([
            'status' => 'success',
            'count' => $cities->count(),
            'data' => $cities
        ]);
    }

    /**
     * API 3: List Customers filtered by State, City, and Search Keywords
     */
    // public function listCustomers(Request $request)
    // {
    //     $customer = Auth::guard('sanctum')->user();

    //     // 1. Validate the logged-in customer/user
    //     if (!$customer || !$customer->admin_id) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Invalid customer data.'
    //         ], 400);
    //     }

    //     // 2. Get search parameter
    //     $search = $request->query('search');

    //     // 3. Base Query to Eager Load relations
    //     $query = Customer::with(['village', 'familyMembers'])
    //         ->where('admin_id', $customer->admin_id);

    //     // 4. Apply search filter
    //     if ($search) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'LIKE', '%' . $search . '%')
    //                 ->orWhere('mobile', 'LIKE', '%' . $search . '%')
    //                 ->orWhere('whatsapp', 'LIKE', '%' . $search . '%')
    //                 ->orWhere('email', 'LIKE', '%' . $search . '%')
    //                 ->orWhere('business_name', 'LIKE', '%' . $search . '%')
    //                 ->orWhere('business_type', 'LIKE', '%' . $search . '%')
    //                 ->orWhere('gotra', 'LIKE', '%' . $search . '%')
    //                 ->orWhere('father_name', 'LIKE', '%' . $search . '%');
    //         });
    //     }

    //     $customers = $query->get();

    //     // 5. Get IDs of all customers under this admin that this user HAS NOT seen yet
    //     $unseenCustomerIds = Customer::where('admin_id', $customer->admin_id)
    //         ->whereDoesntHave('viewers', function ($q) use ($customer) {
    //             $q->where('user_id', $customer->id);
    //         })
    //         ->pluck('id')
    //         ->toArray();

    //     // The count of new items before we clear them
    //     $newCustomersCount = count($unseenCustomerIds);

    //     // 6. FORCE INSERT INTO PIVOT TABLE DIRECTLY
    //     if ($newCustomersCount > 0) {
    //         $insertData = [];
    //         foreach ($unseenCustomerIds as $id) {
    //             $insertData[] = [
    //                 'user_id' => $customer->id,
    //                 'customer_id' => $id,
    //                 'created_at' => \Carbon\Carbon::now(),
    //                 'updated_at' => \Carbon\Carbon::now(),
    //             ];
    //         }

    //         // insertOrIgnore prevents errors if a duplicate entry tries to insert
    //         DB::table('customer_views')->insertOrIgnore($insertData);
    //     }

    //     // Convert to array to transform fields safely
    //     $formattedCustomers = $customers->toArray();

    //     foreach ($formattedCustomers as &$item) {
    //         // Format Dates
    //         if (!empty($item['date_of_birth'])) {
    //             $item['date_of_birth'] = \Carbon\Carbon::parse($item['date_of_birth'])->toIso8601String();
    //         }
    //         if (!empty($item['anniversary_date'])) {
    //             $item['anniversary_date'] = \Carbon\Carbon::parse($item['anniversary_date'])->toIso8601String();
    //         }

    //         // GUARANTEE FULL IMAGE URLS
    //         if (!empty($item['image'])) {
    //             $item['image'] = (strpos($item['image'], 'http') === 0) ? $item['image'] : (
    //                 (strpos($item['image'], 'uploads/') === 0) ? url($item['image']) : url('storage/' . $item['image'])
    //             );
    //         } else {
    //             $item['image'] = null;
    //         }

    //         if (!empty($item['background_image'])) {
    //             $item['background_image'] = (strpos($item['background_image'], 'http') === 0) ? $item['background_image'] : (
    //                 (strpos($item['background_image'], 'uploads/') === 0) ? url($item['background_image']) : url('storage/' . $item['background_image'])
    //             );
    //         } else {
    //             $item['background_image'] = null;
    //         }
    //     }

    //     // 7. Return response
    //     return response()->json([
    //         'status' => 'success',
    //         'total_count' => count($formattedCustomers),
    //         'new_items_count' => $newCustomersCount,
    //         'data' => $formattedCustomers
    //     ]);
    // }

    public function listCustomers(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // 1. Validate the logged-in customer/user
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // 2. Get search parameters
        $search = $request->query('search');
        $state = $request->query('state');
        $city = $request->query('city');

        // 3. Base Query to Eager Load relations
        $query = Customer::with(['village', 'familyMembers'])
            ->where('admin_id', $customer->admin_id);

        // Filter by state
        if ($state) {
            $query->where('state', $state);
        }

        // Filter by city
        if ($city) {
            $query->where('city', $city);
        }

        // 4. Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                    ->orWhere('whatsapp', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('business_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('business_type', 'LIKE', '%' . $search . '%')
                    ->orWhere('gotra', 'LIKE', '%' . $search . '%')
                    ->orWhere('father_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('city', 'LIKE', '%' . $search . '%')
                    ->orWhere('state', 'LIKE', '%' . $search . '%');
            });
        }

        // Order alphabetically by name (A to Z)
        $customers = $query->orderBy('name', 'asc')->get();

        // 5. Get IDs of all customers under this admin that this user HAS NOT seen yet
        $unseenCustomerIds = Customer::where('admin_id', $customer->admin_id)
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })
            ->pluck('id')
            ->toArray();

        // The count of new items before we clear them
        $newCustomersCount = count($unseenCustomerIds);

        // 6. FORCE INSERT INTO PIVOT TABLE DIRECTLY
        if ($newCustomersCount > 0) {
            $insertData = [];
            $now = \Carbon\Carbon::now();
            foreach ($unseenCustomerIds as $id) {
                $insertData[] = [
                    'user_id' => $customer->id,
                    'customer_id' => $id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // insertOrIgnore prevents errors if a duplicate entry tries to insert
            DB::table('customer_views')->insertOrIgnore($insertData);
        }

        // Convert to array to transform fields safely
        $formattedCustomers = $customers->toArray();

        foreach ($formattedCustomers as &$item) {
            // Format Dates
            if (!empty($item['date_of_birth'])) {
                $item['date_of_birth'] = \Carbon\Carbon::parse($item['date_of_birth'])->toIso8601String();
            }
            if (!empty($item['anniversary_date'])) {
                $item['anniversary_date'] = \Carbon\Carbon::parse($item['anniversary_date'])->toIso8601String();
            }

            // GUARANTEE FULL IMAGE URLS
            if (!empty($item['image'])) {
                $item['image'] = (strpos($item['image'], 'http') === 0) ? $item['image'] : (
                    (strpos($item['image'], 'uploads/') === 0) ? url($item['image']) : url('storage/' . $item['image'])
                );
            } else {
                $item['image'] = null;
            }

            if (!empty($item['background_image'])) {
                $item['background_image'] = (strpos($item['background_image'], 'http') === 0) ? $item['background_image'] : (
                    (strpos($item['background_image'], 'uploads/') === 0) ? url($item['background_image']) : url('storage/' . $item['background_image'])
                );
            } else {
                $item['background_image'] = null;
            }
        }
        unset($item); // Break the reference

        // 7. Return response
        return response()->json([
            'status' => 'success',
            'total_count' => count($formattedCustomers),
            'new_items_count' => $newCustomersCount,
            'data' => $formattedCustomers
        ]);
    }

    /**
     * Display details of a specific customer from the same admin
     */
    public function showCustomer(Request $request, $id)
    {
        $customer = Auth::guard('sanctum')->user();

        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // Get the specific customer from the same admin
        $targetCustomer = Customer::with('village')
            ->where('id', $id)

            ->first();

        // Check if customer exists and belongs to the same admin
        if (!$targetCustomer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found or access denied.'
            ], 404);
        }

        // Convert to array to explicitly sanitize/guarantee asset prefix paths
        $targetArray = $targetCustomer->toArray();

        // GUARANTEE FULL IMAGE URLS FOR SHOW INDIVIDUAL VIEW
        if (!empty($targetArray['image'])) {
            $targetArray['image'] = (strpos($targetArray['image'], 'http') === 0) ? $targetArray['image'] : (
                (strpos($targetArray['image'], 'uploads/') === 0) ? url($targetArray['image']) : url('storage/' . $targetArray['image'])
            );
        } else {
            $targetArray['image'] = null;
        }

        if (!empty($targetArray['background_image'])) {
            $targetArray['background_image'] = (strpos($targetArray['background_image'], 'http') === 0) ? $targetArray['background_image'] : (
                (strpos($targetArray['background_image'], 'uploads/') === 0) ? url($targetArray['background_image']) : url('storage/' . $targetArray['background_image'])
            );
        } else {
            $targetArray['background_image'] = null;
        }

        return response()->json([
            'status' => 'success',
            'data' => $targetArray
        ]);
    }

    public function getSocialLinks(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // 1. Validate the logged-in customer session
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // 2. Fetch the links matching the customer's admin_id
        $links = Link::where('admin_id', $customer->admin_id)->first();

        // 3. If no links are configured yet, return fallback empty values
        if (!$links) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'whatsapp_link'  => null,
                    'facebook_link'  => null,
                    'email_link'     => null,
                    'twitter_link'   => null,
                    'instagram_link' => null,
                    'linkedin_link'  => null,
                ]
            ]);
        }

        // 4. Return the configured links matching your API standard structure
        return response()->json([
            'status' => 'success',
            'data' => [
                // 'id'             => $links->id,
                // 'admin_id'       => $links->admin_id,
                'whatsapp_link'  => $links->whatsapp_link,
                'facebook_link'  => $links->facebook_link,
                'email_link'     => $links->email_link,
                'twitter_link'   => $links->twitter_link,
                'instagram_link' => $links->instagram_link,
                'linkedin_link'  => $links->linkedin_link,
            ]
        ]);
    }

    /**
     * Display gallery items from the customer's admin
     */
    public function gallery(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // 1. Get gallery items from the same admin
        $galleryItems = GalleryItem::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Identify newly added items unseen by this user
        $unseenIds = GalleryItem::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })
            ->pluck('id')
            ->toArray();

        $newItemsCount = count($unseenIds);

        // 3. Force insert to pivot table immediately so it counts as read
        if ($newItemsCount > 0) {
            $insertData = [];
            foreach ($unseenIds as $id) {
                $insertData[] = [
                    'user_id' => $customer->id,
                    'gallery_item_id' => $id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            DB::table('gallery_views')->insertOrIgnore($insertData);
        }

        // Transform full URLs
        $galleryItemsWithUrls = $galleryItems->map(function ($item) {
            $itemArray = $item->toArray();
            
            // Overwrite original path arrays with the full URLs
            $itemArray['image_paths'] = $item->image_paths_url;
            $itemArray['video_paths'] = $item->video_paths_url;
            
            // Remove the temporary URL fields
            unset($itemArray['image_paths_url']);
            unset($itemArray['video_paths_url']);
            
            return $itemArray;
        });

        return response()->json([
            'status' => 'success',
            'new_items_count' => $newItemsCount, // Displays count first time, then drops to 0
            'data' => $galleryItemsWithUrls
        ]);
    }

    public function event(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // 1. Get active events with your custom customer RSVP
        $events = Event::query()
            ->where('status', 'active')
            ->with(['rsvps' => function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Identify newly added items unseen by this user
        $unseenIds = Event::query()
            ->where('status', 'active')
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })
            ->pluck('id')
            ->toArray();

        $newItemsCount = count($unseenIds);

        // 3. Force insert to pivot table immediately
        if ($newItemsCount > 0) {
            $insertData = [];
            foreach ($unseenIds as $id) {
                $insertData[] = [
                    'user_id' => $customer->id,
                    'event_id' => $id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            DB::table('event_views')->insertOrIgnore($insertData);
        }

        $eventsWithUrls = $events->map(function ($event) {
            $eventArray = $event->toArray();
            
            // Overwrite original path arrays with the full URLs
            $eventArray['image_paths'] = $event->image_paths_url;
            $eventArray['image_path'] = $event->image_path_url;
            
            // Remove the temporary URL fields
            unset($eventArray['image_paths_url']);
            unset($eventArray['image_path_url']);

            $eventArray['rsvp_status'] = null;
            $eventArray['rsvp_note'] = null;
            $eventArray['adults_count'] = null;
            $eventArray['children_count'] = null;

            if (isset($eventArray['rsvps']) && count($eventArray['rsvps']) > 0) {
                $rsvp = $eventArray['rsvps'][0];
                $status = $rsvp['status'] ?? null;
                $adultsCount = $rsvp['adults_count'] ?? 0;
                $childrenCount = $rsvp['children_count'] ?? 0;

                // Handle legacy bad data where status is accepted but guest count is 0
                if ($status === 'accepted' && ($adultsCount + $childrenCount) == 0) {
                    $status = null; // Reset status so it doesn't show as accepted
                }

                $eventArray['rsvp_status'] = $status;
                $eventArray['rsvp_note'] = $rsvp['note'] ?? null;
                $eventArray['adults_count'] = $adultsCount;
                $eventArray['children_count'] = $childrenCount;
            }

            $isPastDeadline = \Carbon\Carbon::now()->startOfDay() > \Carbon\Carbon::parse($event->posted_date)->startOfDay();

            $eventArray['can_edit_rsvp'] = !$isPastDeadline;

            if ($eventArray['can_edit_rsvp']) {
                $eventArray['event_timeline_status'] = null;
            } else {
                if ($event->date) {
                    if (\Carbon\Carbon::now()->startOfDay() > \Carbon\Carbon::parse($event->date)->startOfDay()) {
                        $eventArray['event_timeline_status'] = 'Event end date has passed';
                    } elseif (\Carbon\Carbon::now()->isSameDay($event->date)) {
                        $eventArray['event_timeline_status'] = 'ongoing';
                    } else {
                        $eventArray['event_timeline_status'] = 'upcoming';
                    }
                } else {
                    // Fallback to old behavior if date is null
                    if ($isPastDeadline) {
                        $eventArray['event_timeline_status'] = 'Event end date has passed';
                    } elseif (\Carbon\Carbon::now()->isSameDay($event->posted_date)) {
                        $eventArray['event_timeline_status'] = 'ongoing';
                    } else {
                        $eventArray['event_timeline_status'] = 'upcoming';
                    }
                }
            }

            unset($eventArray['rsvps']);
            return $eventArray;
        });

        return response()->json([
            'status' => 'success',
            'new_items_count' => $newItemsCount,
            'data' => $eventsWithUrls
        ]);
    }

    public function news(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // 1. Get active news
        $newsItems = News::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('posted_date', 'desc')
            ->get();

        // 2. Identify newly added items unseen by this user
        $unseenIds = News::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })
            ->pluck('id')
            ->toArray();

        $newItemsCount = count($unseenIds);

        // 3. Force insert to pivot table immediately
        if ($newItemsCount > 0) {
            $insertData = [];
            foreach ($unseenIds as $id) {
                $insertData[] = [
                    'user_id' => $customer->id,
                    'news_id' => $id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            DB::table('news_views')->insertOrIgnore($insertData);
        }

        $newsItemsWithUrls = $newsItems->map(function ($news) {
            $newsArray = $news->toArray();
            $newsArray['image_path_url'] = $news->image_path_url;
            return $newsArray;
        });

        return response()->json([
            'status' => 'success',
            'new_items_count' => $newItemsCount,
            'data' => $newsItemsWithUrls
        ]);
    }

    /**
     * Display banner items from the customer's admin
     */
    public function banner(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $banners = Banner::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        // Transform each banner to include the full URL in 'image_path'
        $bannersWithUrls = $banners->map(function ($banner) {
            $bannerArray = $banner->toArray();

            // Overwrite image_path with the full URL
            $bannerArray['image_path'] = $banner->image_path_url;

            // Optional: remove image_path_url if you no longer need both fields
            unset($bannerArray['image_path_url']);

            return $bannerArray;
        });

        return response()->json([
            'status' => 'success',
            'data' => $bannersWithUrls
        ]);
    }

    /**
     * Display notice items from the customer's admin
     */
    public function notice(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get notice items from the same admin
        $notices = Notice::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $notices
        ]);
    }

    /**
     * Display village items from the customer's admin
     */
    public function village(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get village items from the same admin
        $villages = Village::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $villages
        ]);
    }

    /**
     * Display event items from the customer's admin
     */


    /**
     * Display support items from the customer's admin
     */
    public function support(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get support items from the same admin
        $supports = Support::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $supports
        ]);
    }

    public function committee(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // 1. Get active committee members ordered by sort_order
        $committeeMembers = CommitteePerson::query()
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get();

        // 2. Identify newly added items unseen by this user
        $unseenIds = CommitteePerson::query()
            ->where('status', 'active')
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })
            ->pluck('id')
            ->toArray();

        $newItemsCount = count($unseenIds);

        // 3. Force insert to pivot table immediately
        if ($newItemsCount > 0) {
            $insertData = [];
            foreach ($unseenIds as $id) {
                $insertData[] = [
                    'user_id' => $customer->id,
                    'committee_person_id' => $id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            DB::table('committee_views')->insertOrIgnore($insertData);
        }

        // 4. Pre-fetch matching customers to prevent N+1 performance issues
        $phones = $committeeMembers->pluck('phone')->filter()->unique();
        $customers = Customer::with(['village', 'familyMembers', 'category', 'subcategory'])
            ->whereIn('mobile', $phones)
            ->get()
            ->keyBy('mobile');

        // 5. Map and flatten committee member data alongside all customer details
        $data = $committeeMembers->map(function ($member) use ($customers) {
            $memberArray = $member->toArray();
            $customerData = $member->phone ? $customers->get($member->phone) : null;

            // If a customer match is found, merge all their attributes into the main array
            if ($customerData) {
                $customerArray = $customerData->toArray();

                // Format URLs for main customer images/files
                if (!empty($customerArray['image'])) {
                    $customerArray['image'] = (strpos($customerArray['image'], 'uploads/') === 0)
                        ? url($customerArray['image'])
                        : url('storage/' . $customerArray['image']);
                }
                if (!empty($customerArray['background_image'])) {
                    $customerArray['background_image'] = (strpos($customerArray['background_image'], 'uploads/') === 0)
                        ? url($customerArray['background_image'])
                        : url('storage/' . $customerArray['background_image']);
                }
                if (!empty($customerArray['pdf'])) {
                    $customerArray['pdf'] = (strpos($customerArray['pdf'], 'uploads/') === 0)
                        ? url($customerArray['pdf'])
                        : url('storage/' . $customerArray['pdf']);
                }

                // Format URLs for family members images and pdfs
                if (!empty($customerArray['family_members'])) {
                    foreach ($customerArray['family_members'] as &$familyMember) {
                        if (!empty($familyMember['image'])) {
                            $familyMember['image'] = (strpos($familyMember['image'], 'uploads/') === 0)
                                ? url($familyMember['image'])
                                : url('storage/' . $familyMember['image']);
                        }
                        if (!empty($familyMember['pdf'])) {
                            $familyMember['pdf'] = (strpos($familyMember['pdf'], 'uploads/') === 0)
                                ? url($familyMember['pdf'])
                                : url('storage/' . $familyMember['pdf']);
                        }
                    }
                }

                // Merge all customer fields directly into the main committee member array
                // (Unset ID from customer to prevent overwriting committee person's ID)
                unset($customerArray['id']);
                $memberArray = array_merge($memberArray, $customerArray);
            }

            // Determine final image_path / image (Prioritize customer profile image over committee image)
            $imageToUse = null;
            if ($customerData && !empty($customerData->image)) {
                $imageToUse = $customerData->image;
            } elseif (!empty($member->image_path)) {
                $imageToUse = $member->image_path;
            }

            if ($imageToUse) {
                $formattedImage = (strpos($imageToUse, 'uploads/') === 0)
                    ? url($imageToUse)
                    : url('storage/' . $imageToUse);

                $memberArray['image_path'] = $formattedImage;
                $memberArray['image'] = $formattedImage;
            } else {
                $memberArray['image_path'] = null;
                $memberArray['image'] = null;
            }

            // Determine final PDF
            $pdfToUse = null;
            if ($customerData && !empty($customerData->pdf)) {
                $pdfToUse = $customerData->pdf;
            } elseif (!empty($member->pdf)) {
                $pdfToUse = $member->pdf;
            }

            if ($pdfToUse) {
                $memberArray['pdf'] = (strpos($pdfToUse, 'uploads/') === 0)
                    ? url($pdfToUse)
                    : url('storage/' . $pdfToUse);
            } else {
                $memberArray['pdf'] = null;
            }

            return $memberArray;
        });
        // Group the data by state
        $groupedData = $data->groupBy(function ($item) {
            return !empty($item['state']) ? $item['state'] : 'National';
        });

        return response()->json([
            'status' => 'success',
            'new_items_count' => $newItemsCount,
            'data' => $groupedData
        ]);
    }

    /**
     * Display customer plan items for the specific customer
     */
    public function customerPlan(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get customer plan items for the specific customer
        $customerPlans = CustomerPlan::with('customer')
            ->where('customer_id', $customer->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(6); // Show 6 plans per page

        return response()->json([
            'status' => 'success',
            'data' => $customerPlans
        ]);
    }

    /**
     * Display the About Us content for the customer's admin
     */
    public function aboutUs(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // Get the About Us content for the customer's admin
        $aboutUs = $customer->admin->aboutUs;

        // If no About Us content exists, return default data
        if (!$aboutUs) {
            $aboutUs = [
                'description' => 'No information available.',
                'vision' => 'No information available.',
                'mission' => 'No information available.',
                'image_path' => null
            ];
        } else {
            // Only return the fields we need
            $aboutUs = [
                'description' => $aboutUs->description,
                'vision' => $aboutUs->vision,
                'mission' => $aboutUs->mission,
                'image_path' => $aboutUs->image_path
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $aboutUs
        ]);
    }

    /**
     * Display a specific gallery item from the customer's admin
     */
    public function showGalleryItem(Request $request, $id)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get the specific gallery item from the same admin
        $galleryItem = GalleryItem::where('id', $id)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->first();

        // Check if gallery item exists and belongs to the same admin
        if (!$galleryItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gallery item not found or access denied.'
            ], 404);
        }

        $galleryItemArray = $galleryItem->toArray();
        $galleryItemArray['image_paths_url'] = $galleryItem->image_paths_url;
        $galleryItemArray['video_paths_url'] = $galleryItem->video_paths_url;

        return response()->json([
            'status' => 'success',
            'data' => $galleryItemArray
        ]);
    }

    /**
     * Display a specific notice item from the customer's admin
     */
    public function showNoticeItem(Request $request, $id)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get the specific notice item from the same admin
        $noticeItem = Notice::where('id', $id)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->first();

        // Check if notice item exists and belongs to the same admin
        if (!$noticeItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notice item not found or access denied.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $noticeItem
        ]);
    }

    /**
     * Display a specific support item from the customer's admin
     */
    public function showSupportItem(Request $request, $id)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get the specific support item from the same admin
        $supportItem = Support::where('id', $id)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->first();

        // Check if support item exists and belongs to the same admin
        if (!$supportItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Support item not found or access denied.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $supportItem
        ]);
    }

    /**
     * Display a specific customer plan item for the customer
     */
    public function showCustomerPlan(Request $request, $id)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get the specific customer plan item for the customer
        $customerPlan = CustomerPlan::with('customer')
            ->where('id', $id)
            ->where('customer_id', $customer->id)
            ->where('status', 'active')
            ->first();

        // Check if customer plan exists and belongs to the customer
        if (!$customerPlan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer plan not found or access denied.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $customerPlan
        ]);
    }

    /**
     * Display a list of family members for the customer
     */
    public function listFamilyMembers(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get all family members for the customer
        $familyMembers = $customer->familyMembers()->get();

        // Add full image and PDF URLs to each member
        $familyMembers->transform(function ($member) {
            if ($member->image) {
                $member->image = url($member->image);
            }
            if ($member->pdf) {
                $member->pdf = url($member->pdf);
            }
            return $member;
        });

        return response()->json([
            'status' => 'success',
            'data' => $familyMembers
        ]);
    }

    /**
     * Display a list of family members biolinks for the customer
     */
    public function listFamilyMemberBioLinks(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $bioLinks = $customer->familyMembers()
            ->select('id', 'name', 'biolink')
            ->get()
            ->map(function ($member) {
                // If biolink is empty, set to default
                if (empty($member->biolink)) {
                    $member->biolink = 'https://jainwed.com/biodata';
                }
                // Prepend the base URL if it's not already a full URL
                elseif (!str_starts_with($member->biolink, 'http')) {
                    $member->biolink = 'https://jainwed.com/biodata/' . ltrim($member->biolink, '/');
                }
                return $member;
            });

        return response()->json([
            'status' => 'success',
            'data' => $bioLinks
        ]);
    }

    /**
     * Display details of a specific family member
     */
    public function showFamilyMember(Request $request, $id)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get the specific family member for the customer
        $familyMember = $customer->familyMembers()->where('id', $id)->first();

        // Check if family member exists and belongs to the customer
        if (!$familyMember) {
            return response()->json([
                'status' => 'error',
                'message' => 'Family member not found or access denied.'
            ], 404);
        }

        // Add full image and PDF URLs
        if ($familyMember->image) {
            $familyMember->image = url($familyMember->image);
        }
        if ($familyMember->pdf) {
            $familyMember->pdf = url($familyMember->pdf);
        }

        return response()->json([
            'status' => 'success',
            'data' => $familyMember
        ]);
    }

    /**
     * Create a new family member for the customer
     */
    public function createFamilyMember(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'relationship' => 'nullable|string|max:100',
            'mobile' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'anniversary_date' => 'nullable|date',
            'gotra' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'education' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'hobbies' => 'nullable|string|max:255',
            'native_place' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'matrimony' => 'nullable|in:1,0,true,false,"1","0"',
            'gender' => 'nullable|string|in:male,female,other',
            'link' => 'nullable|string|max:255',
            'pdf' => 'nullable|file|mimes:pdf|max:5048',
            'biolink' => 'nullable|string|max:255',
        ]);

        // Handle Image file
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/family_members'), $imageName);
            $validatedData['image'] = 'uploads/family_members/' . $imageName;
        }

        // Handle PDF file
        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $pdfName = time() . '.' . $pdf->extension();
            $pdf->move(public_path('uploads/family_pdfs'), $pdfName);
            $validatedData['pdf'] = 'uploads/family_pdfs/' . $pdfName;
        }

        // Set default biolink during creation
        if (empty($validatedData['biolink'])) {
            $validatedData['biolink'] = 'https://jainwed.com/biodata';
        }

        $familyMember = new FamilyMember($validatedData);
        $familyMember->customer_id = $customer->id;
        $familyMember->save();

        // Convert to array and generate full URLs for the API response
        $responseData = $familyMember->toArray();
        if (!empty($familyMember->image)) {
            $responseData['image'] = url($familyMember->image);
        }
        if (!empty($familyMember->pdf)) {
            $responseData['pdf'] = url($familyMember->pdf);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Family member created successfully!',
            'data' => $responseData
        ]);
    }

    /**
     * Update a specific family member for the customer
     */
    public function updateFamilyMember(Request $request, $id)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get the specific family member for the customer
        $familyMember = $customer->familyMembers()->where('id', $id)->first();

        // Check if family member exists and belongs to the customer
        if (!$familyMember) {
            return response()->json([
                'status' => 'error',
                'message' => 'Family member not found or access denied.'
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'relationship' => 'nullable|string|max:100',
            'mobile' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'anniversary_date' => 'nullable|date',
            'gotra' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'education' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'hobbies' => 'nullable|string|max:255',
            'native_place' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'matrimony' => 'nullable|in:1,0,true,false,"1","0"',
            'gender' => 'nullable|string|in:male,female,other',
            'link' => 'nullable|string|max:255',
            'pdf' => 'nullable|file|mimes:pdf|max:5048',
            'biolink' => 'nullable|string|max:255',
        ]);

        // Handle Image file replacement
        if ($request->hasFile('image')) {
            if ($familyMember->image && File::exists(public_path($familyMember->image))) {
                File::delete(public_path($familyMember->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/family_members'), $imageName);
            $validatedData['image'] = 'uploads/family_members/' . $imageName;
        }

        // Handle PDF file replacement
        if ($request->hasFile('pdf')) {
            if ($familyMember->pdf && File::exists(public_path($familyMember->pdf))) {
                File::delete(public_path($familyMember->pdf));
            }

            $pdf = $request->file('pdf');
            $pdfName = time() . '.' . $pdf->extension();
            $pdf->move(public_path('uploads/family_pdfs'), $pdfName);
            $validatedData['pdf'] = 'uploads/family_pdfs/' . $pdfName;
        }

        $familyMember->update($validatedData);

        // Convert to array and generate full URLs for the API response
        $responseData = $familyMember->toArray();
        if (!empty($familyMember->image)) {
            $responseData['image'] = url($familyMember->image);
        }
        if (!empty($familyMember->pdf)) {
            $responseData['pdf'] = url($familyMember->pdf);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Family member updated successfully!',
            'data' => $responseData
        ]);
    }

    /**
     * Delete a specific family member for the customer
     */
    public function deleteFamilyMember(Request $request, $id)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get the specific family member for the customer
        $familyMember = $customer->familyMembers()->where('id', $id)->first();

        // Check if family member exists and belongs to the customer
        if (!$familyMember) {
            return response()->json([
                'status' => 'error',
                'message' => 'Family member not found or access denied.'
            ], 404);
        }

        $familyMember->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Family member deleted successfully!'
        ]);
    }

    /**
     * Display a list of polls from the customer's admin
     */
    public function listPolls(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get polls from the same admin
        $polls = Poll::where('admin_id', $customer->admin_id)
            ->where('active', true)
            ->with(['responses' => function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            }])
            ->withCount(['responses as yes_count' => function ($query) {
                $query->where('response', 'yes');
            }])
            ->withCount(['responses as no_count' => function ($query) {
                $query->where('response', 'no');
            }])
            ->withCount(['responses as maybe_count' => function ($query) {
                $query->where('response', 'maybe');
            }])
            ->withCount('responses as total_responses')
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $polls
        ]);
    }

    /**
     * Vote on a specific poll
     */
    public function voteOnPoll(Request $request, $pollId)
    {
        $customer = Auth::guard('sanctum')->user();

        // Validate the poll belongs to the customer's admin and is active
        $poll = Poll::where('id', $pollId)
            ->where('admin_id', $customer->admin_id)
            ->where('active', true)
            ->first();

        if (!$poll) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid poll.'
            ], 404);
        }

        // Validate the response
        $validatedData = $request->validate([
            'response' => 'required|in:yes,no,maybe',
        ]);

        // Check if customer has already voted
        $existingVote = PollResponse::where('poll_id', $poll->id)
            ->where('customer_id', $customer->id)
            ->first();

        if ($existingVote) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already voted on this poll.'
            ], 400);
        }

        // Save the response
        $pollResponse = PollResponse::create([
            'poll_id' => $poll->id,
            'customer_id' => $customer->id,
            'response' => $validatedData['response'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you for your vote!',
            'data' => $pollResponse
        ]);
    }

    /**
     * Get today's birthdays from admin-added customers
     */
    public function todayBirthdays(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // 1. Get all customers with birthdays (Eager loading full village relation like showCustomer)
        $allBirthdays = Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('date_of_birth')
            ->with('village') // Changed from 'village:id,name' to load full schema data
            ->get();

        $today = \Carbon\Carbon::now();
        $todayMonth = $today->month;
        $todayDay = $today->day;
        $todayCount = 0;

        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        // Initialize all 12 months with empty standard structural objects
        $finalData = [];
        foreach ($monthNames as $num => $name) {
            $finalData[$name] = new \stdClass();
        }

        // 2. Format dates, append contextual keys, filter, and sort
        $processedBirthdays = $allBirthdays->map(function ($item) use ($todayMonth, $todayDay, &$todayCount, $monthNames, $today) {
            $dob = \Carbon\Carbon::parse($item->date_of_birth);

            // Check if the birthday is exactly TODAY
            if ($dob->month === $todayMonth && $dob->day === $todayDay) {
                $todayCount++;
                $item->is_birthday_today = true;
            } else {
                $item->is_birthday_today = false;
            }

            // Structural extensions to match the application dashboard rules
            $item->birth_month_name = $monthNames[$dob->month];
            $item->birth_month_num = $dob->month;
            $item->birth_day = $dob->day;

            // Dynamic display timeline index grouping string
            $item->birthday_group_date = sprintf('%02d-%02d-%d', $dob->day, $dob->month, $today->year);

            // Convert birth dates to structured ISO format
            $item->date_of_birth = $dob->toIso8601String();

            // Ensure standard object structural fields exist safely in the response payload
            if (!isset($item->anniversary_date)) {
                $item->anniversary_date = null;
            }

            // Format URLs for images and PDFs
            if (!empty($item->image)) {
                $item->image = (strpos($item->image, 'uploads/') === 0) ? url($item->image) : url('storage/' . $item->image);
            }
            if (!empty($item->background_image)) {
                $item->background_image = (strpos($item->background_image, 'uploads/') === 0) ? url($item->background_image) : url('storage/' . $item->background_image);
            }
            if (!empty($item->pdf)) {
                $item->pdf = (strpos($item->pdf, 'uploads/') === 0) ? url($item->pdf) : url('storage/' . $item->pdf);
            }

            return $item;
        })
            // Filter out past birthdays for the current month
            ->filter(function ($item) use ($todayMonth, $todayDay) {
                if ($item->birth_month_num === $todayMonth) {
                    return $item->birth_day >= $todayDay;
                }
                return true;
            })
            // Sort chronologically by calendar day
            ->sortBy('birth_day');

        // 3. Group data collections chronologically
        $groupedByMonthNum = $processedBirthdays->groupBy('birth_month_num');

        foreach ($groupedByMonthNum as $monthNum => $monthGroup) {
            $monthName = $monthNames[$monthNum];

            // Group customers into key-value pairs matching their upcoming milestone date
            $dateGroupings = $monthGroup->groupBy('birthday_group_date')->map(function ($customersOnDate) {
                return $customersOnDate->values()->all();
            });

            $finalData[$monthName] = $dateGroupings;
        }

        return response()->json([
            'status' => 'success',
            'today_count' => $todayCount,
            'data' => $finalData
        ]);
    }

    /**
     * Get today's anniversaries from admin-added customers
     */
    public function todayAnniversaries(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // 1. Get all customers with anniversaries (Eager loading full village relation like showCustomer)
        $allAnniversaries = Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('anniversary_date')
            ->with('village') // Loads full schema structural properties
            ->get();

        $today = \Carbon\Carbon::now();
        $todayMonth = $today->month;
        $todayDay = $today->day;
        $todayCount = 0;

        // Array to map month numbers to their full text names
        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        // Initialize all 12 months with empty standard structural objects
        $finalData = [];
        foreach ($monthNames as $num => $name) {
            $finalData[$name] = new \stdClass(); // Uses standard object class so empty months output as {} instead of []
        }

        // 2. Format dates, append contextual keys, filter, and sort
        $processedAnniversaries = $allAnniversaries->map(function ($item) use ($todayMonth, $todayDay, &$todayCount, $monthNames, $today) {
            $anniversaryDate = \Carbon\Carbon::parse($item->anniversary_date);

            // Check if the anniversary is exactly TODAY
            if ($anniversaryDate->month === $todayMonth && $anniversaryDate->day === $todayDay) {
                $todayCount++;
                $item->is_anniversary_today = true;
            } else {
                $item->is_anniversary_today = false;
            }

            // Structural extensions to match the application dashboard rules
            $item->anniversary_month_name = $monthNames[$anniversaryDate->month];
            $item->anniversary_month_num = $anniversaryDate->month;
            $item->anniversary_day = $anniversaryDate->day;

            // Dynamic display timeline index grouping string
            $item->anniversary_group_date = sprintf('%02d-%02d-%d', $anniversaryDate->day, $anniversaryDate->month, $today->year);

            // Convert anniversary dates to structured ISO format
            $item->anniversary_date = $anniversaryDate->toIso8601String();

            // Ensure standard object structural fields exist safely in the response payload
            if (!isset($item->date_of_birth)) {
                $item->date_of_birth = null;
            }

            // Format URLs for images and PDFs
            if (!empty($item->image)) {
                $item->image = (strpos($item->image, 'uploads/') === 0) ? url($item->image) : url('storage/' . $item->image);
            }
            if (!empty($item->background_image)) {
                $item->background_image = (strpos($item->background_image, 'uploads/') === 0) ? url($item->background_image) : url('storage/' . $item->background_image);
            }
            if (!empty($item->pdf)) {
                $item->pdf = (strpos($item->pdf, 'uploads/') === 0) ? url($item->pdf) : url('storage/' . $item->pdf);
            }

            return $item;
        })
            // Filter out past anniversaries for the current month
            ->filter(function ($item) use ($todayMonth, $todayDay) {
                if ($item->anniversary_month_num === $todayMonth) {
                    return $item->anniversary_day >= $todayDay;
                }
                return true;
            })
            // Sort chronologically by calendar day
            ->sortBy('anniversary_day');

        // 3. Group data collections chronologically
        $groupedByMonthNum = $processedAnniversaries->groupBy('anniversary_month_num');

        foreach ($groupedByMonthNum as $monthNum => $monthGroup) {
            $monthName = $monthNames[$monthNum];

            // Group customers into key-value pairs matching their upcoming milestone date
            $dateGroupings = $monthGroup->groupBy('anniversary_group_date')->map(function ($customersOnDate) {
                return $customersOnDate->values()->all();
            });

            $finalData[$monthName] = $dateGroupings;
        }

        return response()->json([
            'status' => 'success',
            'today_count' => $todayCount, // Total count for dashboard badges
            'data' => $finalData
        ]);
    }
    public function dashboardCounters(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer || !$customer->admin_id) {
            return response()->json(['status' => 'error', 'message' => 'Invalid session.'], 400);
        }

        $galleryCount = GalleryItem::where('admin_id', $customer->admin_id)->where('status', 'active')
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })->count();

        $eventCount = Event::where('admin_id', $customer->admin_id)->where('status', 'active')
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })->count();

        $newsCount = News::where('admin_id', $customer->admin_id)->where('status', 'active')
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })->count();

        $committeeCount = CommitteePerson::where('admin_id', $customer->admin_id)->where('status', 'active')
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })->count();

        $customerCount = Customer::where('admin_id', $customer->admin_id)
            ->whereDoesntHave('viewers', function ($q) use ($customer) {
                $q->where('user_id', $customer->id);
            })->count();

        return response()->json([
            'status' => 'success',
            'counters' => [
                'new_gallery_count' => $galleryCount,
                'new_event_count' => $eventCount,
                'new_news_count' => $newsCount,
                'new_committee_count' => $committeeCount,
                'new_customer_count' => $customerCount,
            ]
        ]);
    }

    public function getBusinessCategories(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        $stringCategories = Customer::whereNull('category_id')
            ->whereNotNull('product_service')
            ->where('product_service', '!=', '')
            ->distinct()
            ->pluck('product_service')
            ->toArray();

        $categoryIds = Customer::whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id');

        $dbCategories = Category::whereIn('id', $categoryIds)
            ->pluck('name')
            ->toArray();

        $categories = collect(array_merge($stringCategories, $dbCategories))
            ->filter(function ($val) {
                return !empty($val);
            })
            ->unique()
            ->sort()
            ->values()
            ->map(function ($type, $index) {
                return [
                    'id' => $index + 1,
                    'category_name' => $type
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Get customers filtered by business name
     */
    public function getBusinessNames(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        $category = $request->query('category_name') ?? $request->query('category');
        $search = $request->query('search');

        $query = Customer::query()
            ->whereNotNull('business_name')
            ->where('business_name', '!=', '');

        //  FIX: Filter by product_service string OR category relationship name
        if ($category) {
            $query->where(function ($q) use ($category) {
                $q->where('product_service', $category)
                    ->orWhereHas('category', function ($cq) use ($category) {
                        $cq->where('name', $category);
                    });
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $search . '%');
            });
        }

        $businessNames = $query->selectRaw('business_name, business_type, product_service, office_address, mobile, name, ms_firm_name, category_id, subcategory_id, COUNT(*) as count')
            ->groupBy('business_name', 'business_type', 'product_service', 'office_address', 'mobile', 'name', 'ms_firm_name', 'category_id', 'subcategory_id')
            ->orderBy('business_name', 'asc')
            ->with(['category', 'subcategory'])
            ->get();

        $businessNames->transform(function ($item) {
            if ($item->category && $item->category->name) {
                $item->product_service = $item->category->name;
            }
            if ($item->subcategory && $item->subcategory->name) {
                $item->business_type = $item->subcategory->name;
            }
            return $item;
        });

        return response()->json([
            'status' => 'success',
            'data' => $businessNames
        ]);
    }

    /**
     * Get customers filtered by business name
     */
    public function getCustomersByBusinessName(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        $firmName = $request->query('ms_firm_name') ?? $request->query('business_name');

        if (!$firmName) {
            return response()->json([
                'status' => 'error',
                'message' => 'Firm name is required.'
            ], 400);
        }

        $customers = Customer::query()
            ->where('ms_firm_name', $firmName)
            ->select('id', 'name', 'mobile', 'whatsapp', 'email', 'ms_firm_name', 'business_type', 'product_service', 'office_address', 'village_id', 'category_id', 'subcategory_id')
            ->with(['village:id,name', 'category', 'subcategory'])
            ->get();

        $customers->transform(function ($item) {
            if ($item->category && $item->category->name) {
                $item->product_service = $item->category->name;
            }
            if ($item->subcategory && $item->subcategory->name) {
                $item->business_type = $item->subcategory->name;
            }
            return $item;
        });

        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }

    /**
     * Get all customers grouped by firm name
     */
    public function getCustomersByBusinessCategories(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        $customers = Customer::query()
            ->whereNotNull('ms_firm_name')
            ->where('ms_firm_name', '!=', '')
            ->get();

        $result = $customers->groupBy('ms_firm_name')->map(function ($items, $key) {
            $firstItem = $items->first();

            // FORCED ARRAY KEYS: Exclude 'business_name' completely from the mapping
            return [
                'ms_firm_name'     => (string) $key,
                'business_type'    => $firstItem->business_type ?? null,
                'product_service'  => $firstItem->product_service ?? null,
                'office_address'   => $firstItem->office_address ?? null,
                'mobile'           => $firstItem->mobile ?? null,
                'name'             => $firstItem->name ?? null,
                'count'            => $items->count(),
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data'   => $result
        ]);
    }

    /**
     * Get customers by specific firm name (Route parameter)
     */
    public function getBusinessByName(Request $request, $business)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        $customers = Customer::query()
            ->where('ms_firm_name', $business)
            ->select('id', 'name', 'mobile', 'whatsapp', 'email', 'ms_firm_name', 'business_type', 'product_service', 'office_address', 'village_id')
            ->with('village:id,name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }

    /**
     * Accept or reject an event
     */
    public function respondToEvent(Request $request, $eventId)
    {
        $customer = Auth::guard('sanctum')->user();

        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // Validate the event exists and belongs to the same admin
        $event = Event::where('id', $eventId)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->first();

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found or access denied.'
            ], 404);
        }

        // Validate request data
        $validatedData = $request->validate([
            'status' => 'required|in:accepted,declined,maybe',
            'note' => 'nullable|string|max:500'
        ]);

        // Only require adults_count and children_count for accepted status
        if ($request->status === 'accepted') {
            $request->validate([
                'adults_count' => 'required|integer|min:0',
                'children_count' => 'required|integer|min:0'
            ]);
        }

        // Create or update RSVP
        $rsvp = EventRSVP::updateOrCreate(
            [
                'event_id' => $event->id,
                'customer_id' => $customer->id
            ],
            [
                'status' => $validatedData['status'],
                'note' => $validatedData['note'] ?? null,
                'adults_count' => $request->adults_count ?? 0,
                'children_count' => $request->children_count ?? 0
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Event response recorded successfully.',
            'data' => $rsvp
        ]);
    }

    /**
     * Get RSVP status for a specific event
     */
    public function getEventResponse(Request $request, $eventId)
    {
        $customer = Auth::guard('sanctum')->user();

        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // Validate the event exists and belongs to the same admin
        $event = Event::where('id', $eventId)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->first();

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found or access denied.'
            ], 404);
        }

        // Get RSVP status
        $rsvp = EventRSVP::where('event_id', $event->id)
            ->where('customer_id', $customer->id)
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $rsvp
        ]);
    }

    /**
     * Mark a notification as read (and effectively remove it from the list)
     */
    public function markRead(Request $request, $id)
    {
        $customer = Auth::guard('sanctum')->user();

        $notification = $customer->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found.'
            ], 404);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read successfully!'
        ]);
    }

    // /**
    //  * Mark all notifications as read
    //  */
    public function markallreadnotifications(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $customer->notifications()->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read successfully!'
        ]);
    }
    
    // /**
    //  * Get count of unread notifications
    //  */
    // public function unreadNotificationsCount(Request $request)
    // {
    //     $customer = Auth::guard('sanctum')->user();
        
    //     $count = $customer->notifications()->where('is_read', false)->count();
        
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => ['count' => $count]
    //     ]);
    // }

    /**
     * Test real-time notification (for testing purposes)
     */
    // 1. Method to save the Token from the phone
    public function updateDeviceToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        $customer = Auth::guard('sanctum')->user();
        $customer->fcm_token = $request->fcm_token;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Device token updated successfully'
        ]);
    }

    // 2. Method to test the Firebase connection
    public function testRealTimeNotification(Request $request, \App\Services\RealTimeNotificationService $realTimeService)
    {
        $customer = Auth::guard('sanctum')->user();

        $result = $realTimeService->sendRealTimeNotification(
            $customer->id,
            'test_real_time',
            'Success! Your Laravel API is now connected to Firebase.'
        );

        return response()->json($result);
    }

    /**
     * Display helpline items from the customer's admin
     */
    public function helpline(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // Get helpline items from the same admin
        $helplines = Helpline::where('admin_id', $customer->admin_id)
            ->orderBy('name')
            ->orderBy('heading_name')
            ->get();

        // Group by name
        $grouped = $helplines->groupBy('name')->map(function ($items, $name) {
            return [
                'name' => $name ?: 'No Name',
                'headings' => $items
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $grouped
        ]);
    }

    /**
     * Get customers with family members who have matrimony set to true
     */
    
    public function deleteProfile(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.'
            ], 401);
        }

        // Reset password related fields
        $customer->password = null;
        $customer->is_password_set = false;
        $customer->otp = null;
        $customer->otp_expires_at = null;
        $customer->save();

        // Revoke all tokens for the customer
        $customer->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Your Profile has been deleted successfully.'
        ]);
    }

    public function getCategories(Request $request)
    {
        $categories = \App\Models\Category::get(['id', 'name']);
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function getSubCategories(Request $request, $id)
    {
        $subcategories = \App\Models\SubCategory::where('category_id', $id)->get(['id', 'name'])->toArray();

        // Add the common default subcategories matching the admin blade view
        $commonSubs = [
            ['id' => 'Retail', 'name' => 'Retail'],
            ['id' => 'Manufacturer', 'name' => 'Manufacturer'],
            ['id' => 'WholeSale', 'name' => 'WholeSale'],
            ['id' => 'Services', 'name' => 'Services'],
            ['id' => 'Professional', 'name' => 'Professional']
        ];

        // Merge common subs, database subs, and append 'Others'
        $allSubs = array_merge($commonSubs, $subcategories);
        $allSubs[] = ['id' => 'Others', 'name' => 'Others'];

        return response()->json([
            'status' => 'success',
            'data' => $allSubs
        ]);
    }

    public function getCustomersWithMatrimony(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // Get parameters
        $search = $request->query('search');
        $gender = $request->query('gender');
        $min_age = $request->query('min_age');
        $max_age = $request->query('max_age');
        $from_dob = $request->query('from_dob');
        $to_dob = $request->query('to_dob');

        // Start Query - Eager load relationships
        $query = Customer::with(['village'])

            ->whereHas('familyMembers', function ($q) use ($gender, $min_age, $max_age, $from_dob, $to_dob) {
                $q->where('matrimony', true);

                // Gender Filter
                if ($gender) {
                    $q->where('gender', $gender);
                }

                // Age Filter (calculated via DOB)
                if ($min_age) {
                    $q->where('date_of_birth', '<=', now()->subYears($min_age)->format('Y-m-d'));
                }
                if ($max_age) {
                    $q->where('date_of_birth', '>=', now()->subYears($max_age + 1)->addDay()->format('Y-m-d'));
                }

                // DOB Range Filter
                if ($from_dob) {
                    $q->where('date_of_birth', '>=', $from_dob);
                }
                if ($to_dob) {
                    $q->where('date_of_birth', '<=', $to_dob);
                }
            });

        // Apply global search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhereHas('familyMembers', function ($fm) use ($search) {
                        $fm->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $customers = $query->get();

        $result = [];
        foreach ($customers as $cust) {

            // 1. Format Customer Images
            $customerImage = null;
            if (!empty($cust->image)) {
                $customerImage = (strpos($cust->image, 'http') === 0) ? $cust->image : (
                    (strpos($cust->image, 'uploads/') === 0) ? url($cust->image) : url('storage/' . $cust->image)
                );
            }

            $customerBackgroundImage = null;
            if (!empty($cust->background_image)) {
                $customerBackgroundImage = (strpos($cust->background_image, 'http') === 0) ? $cust->background_image : (
                    (strpos($cust->background_image, 'uploads/') === 0) ? url($cust->background_image) : url('storage/' . $cust->background_image)
                );
            }

            // Convert customer attributes to array format
            $customerAttributes = $cust->attributesToArray();
            $customerAttributes['village'] = $cust->village ? $cust->village->toArray() : null;
            $customerAttributes['image'] = $customerImage;
            $customerAttributes['background_image'] = $customerBackgroundImage;
            $customerAttributes['customer_age'] = $cust->date_of_birth ? \Carbon\Carbon::parse($cust->date_of_birth)->age : null;

            // 2. Fetch and format the complete list of ALL family members for this customer context
            $allFamilyList = [];
            foreach ($cust->familyMembers()->get() as $member) {
                $mImage = null;
                if (!empty($member->image)) {
                    $mImage = (strpos($member->image, 'http') === 0) ? $member->image : (
                        (strpos($member->image, 'uploads/') === 0) ? url($member->image) : url('storage/' . $member->image)
                    );
                }

                $mPdf = null;
                if (!empty($member->pdf)) {
                    $mPdf = (strpos($member->pdf, 'http') === 0) ? $member->pdf : (
                        (strpos($member->pdf, 'uploads/') === 0) ? url($member->pdf) : url('storage/' . $member->pdf)
                    );
                }

                $memberArray = $member->attributesToArray();
                $memberArray['image'] = $mImage;
                $memberArray['pdf'] = $mPdf;
                $memberArray['age'] = $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->age : null;

                $allFamilyList[] = $memberArray;
            }

            // Nest the complete family list INSIDE the customer details block
            $customerAttributes['all_family_members'] = $allFamilyList;

            // 3. Filter the individual target matrimony candidates
            $filteredTargetMembers = $cust->familyMembers()
                ->where('matrimony', true)
                ->where(function ($q) use ($gender, $min_age, $max_age, $from_dob, $to_dob) {
                    if ($gender) $q->where('gender', $gender);
                    if ($min_age) $q->where('date_of_birth', '<=', now()->subYears($min_age)->format('Y-m-d'));
                    if ($max_age) $q->where('date_of_birth', '>=', now()->subYears($max_age + 1)->addDay()->format('Y-m-d'));
                    if ($from_dob) $q->where('date_of_birth', '>=', $from_dob);
                    if ($to_dob) $q->where('date_of_birth', '<=', $to_dob);
                })->get();

            foreach ($filteredTargetMembers as $fm) {

                // Format Matrimony Target Profile Image
                $familyMemberImage = null;
                if (!empty($fm->image)) {
                    $familyMemberImage = (strpos($fm->image, 'http') === 0) ? $fm->image : (
                        (strpos($fm->image, 'uploads/') === 0) ? url($fm->image) : url('storage/' . $fm->image)
                    );
                }

                // Format Matrimony Target Background Image
                $familyMemberBackgroundImage = null;
                if (!empty($fm->background_image)) {
                    $familyMemberBackgroundImage = (strpos($fm->background_image, 'http') === 0) ? $fm->background_image : (
                        (strpos($fm->background_image, 'uploads/') === 0) ? url($fm->background_image) : url('storage/' . $fm->background_image)
                    );
                }

                // Format Matrimony Target PDF Document URL
                $familyMemberPdf = null;
                if (!empty($fm->pdf)) {
                    $familyMemberPdf = (strpos($fm->pdf, 'http') === 0) ? $fm->pdf : (
                        (strpos($fm->pdf, 'uploads/') === 0) ? url($fm->pdf) : url('storage/' . $fm->pdf)
                    );
                }

                // Convert filtered family member attributes to array format
                $familyMemberAttributes = $fm->attributesToArray();

                // Override values with finalized Absolute URLs
                $familyMemberAttributes['image'] = $familyMemberImage;
                $familyMemberAttributes['background_image'] = $familyMemberBackgroundImage;
                $familyMemberAttributes['pdf'] = $familyMemberPdf;
                $familyMemberAttributes['family_member_age'] = $fm->date_of_birth ? \Carbon\Carbon::parse($fm->date_of_birth)->age : null;

                // Final Deep Structured Dataset matching your required JSON blueprint
                $result[] = [
                    'family_member_details' => $familyMemberAttributes,
                    'customer_details'      => $customerAttributes
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }
}
