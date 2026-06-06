<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\GalleryItem;
use App\Models\CommitteePerson;
use App\Models\Banner;
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
use App\Services\RealTimeNotificationService;
use Carbon\Carbon;

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
        if ($customer->image) {
            $responseData['image'] = (strpos($customer->image, 'uploads/') === 0)
                ? url($customer->image)
                : url('storage/' . $customer->image);
        } else {
            $responseData['image'] = null;
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
            'state' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'business_type' => 'nullable|string|max:100',
            'business_name' => 'nullable|string|max:100',
            'product_service' => 'nullable|string|max:100',
            'office_address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'anniversary_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/customers'), $imageName);
            $validatedData['image'] = 'uploads/customers/' . $imageName;
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

        if (!empty($customer->image)) {
            $responseData['image'] = url($customer->image);
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
            ->where('id', '!=', $customer->id)
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
            ->where('id', '!=', $customer->id)
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
    public function listCustomers(Request $request)
    {
        $customer = $this->getValidatedCustomer();
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Invalid customer data.'], 400);
        }

        $state = $request->query('state');
        $city = $request->query('city');
        $search = $request->query('search');

        // Eager load relationships for full customer profiles
        $query = Customer::with(['village', 'familyMembers'])
            ->where('admin_id', $customer->admin_id)
            ->where('id', '!=', $customer->id);

        // Location context filtering
        if ($state) {
            $query->where('state', $state);
        }
        if ($city) {
            $query->where('city', $city);
        }

        // Detailed search profile filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                    ->orWhere('whatsapp', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('business_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('business_type', 'LIKE', '%' . $search . '%')
                    ->orWhere('gotra', 'LIKE', '%' . $search . '%')
                    ->orWhere('father_name', 'LIKE', '%' . $search . '%');
            });
        }

        $customers = $query->get();

        return response()->json([
            'status' => 'success',
            'count' => $customers->count(),
            'data' => $customers
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
            ->where('admin_id', $customer->admin_id)
            ->first();

        // Check if customer exists and belongs to the same admin
        if (!$targetCustomer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found or access denied.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $targetCustomer
        ]);
    }

    /**
     * Display gallery items from the customer's admin
     */
    public function gallery(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get gallery items from the same admin
        $galleryItems = GalleryItem::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        // Add full image and video URLs to each gallery item
        $galleryItemsWithUrls = $galleryItems->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['image_paths_url'] = $item->image_paths_url;
            $itemArray['video_paths_url'] = $item->video_paths_url;
            return $itemArray;
        });

        return response()->json([
            'status' => 'success',
            'data' => $galleryItemsWithUrls
        ]);
    }

    /**
     * Display banner items from the customer's admin
     */
    public function banner(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get banner items from the same admin
        $banners = Banner::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        // Add full image URLs to each banner
        $bannersWithUrls = $banners->map(function ($banner) {
            $bannerArray = $banner->toArray();
            $bannerArray['image_path_url'] = $banner->image_path_url;
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
    public function event(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get event items from the same admin with RSVP status for the current customer
        $events = Event::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->with(['rsvps' => function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add full image URLs and RSVP status to each event
        $eventsWithUrls = $events->map(function ($event) {
            $eventArray = $event->toArray();
            $eventArray['image_paths_url'] = $event->image_paths_url;
            $eventArray['image_path_url'] = $event->image_path_url;

            // Add RSVP status if the customer has responded to this event
            $eventArray['rsvp_status'] = null;
            $eventArray['rsvp_note'] = null;
            $eventArray['adults_count'] = null;
            $eventArray['children_count'] = null;

            if (isset($eventArray['rsvps']) && count($eventArray['rsvps']) > 0) {
                $rsvp = $eventArray['rsvps'][0]; // Get the first (and should be only) RSVP record
                $eventArray['rsvp_status'] = $rsvp['status'] ?? null;
                $eventArray['rsvp_note'] = $rsvp['note'] ?? null;
                $eventArray['adults_count'] = $rsvp['adults_count'] ?? null;
                $eventArray['children_count'] = $rsvp['children_count'] ?? null;
            }

            // Remove the rsvps array from the response as it's not needed
            unset($eventArray['rsvps']);

            return $eventArray;
        });

        return response()->json([
            'status' => 'success',
            'data' => $eventsWithUrls
        ]);
    }

    /**
     * Display news items from the customer's admin
     */
    public function news(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        // Get news items from the same admin
        $newsItems = News::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('posted_date', 'desc')
            ->get();

        // Add full image URLs to each news item
        $newsItemsWithUrls = $newsItems->map(function ($news) {
            $newsArray = $news->toArray();
            $newsArray['image_path_url'] = $news->image_path_url;
            return $newsArray;
        });

        return response()->json([
            'status' => 'success',
            'data' => $newsItemsWithUrls
        ]);
    }

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

    // 1. Fetch active committee members for this admin, ordered by state and manual sort_order
    $committeeMembers = CommitteePerson::where('admin_id', $customer->admin_id)
        ->where('status', 'active')
        ->orderBy('state', 'asc')
        ->orderBy('sort_order', 'asc')
        ->get();

    // 2. Map through items to keep all original fields and format the image URLs
    $formattedMembers = $committeeMembers->map(function ($member) {
        $memberArray = $member->toArray();

        // Format the full image URL safely
        if ($member->image_path) {
            $memberArray['image_path'] = (strpos($member->image_path, 'uploads/') === 0 || strpos($member->image_path, 'http') === 0)
                ? $member->image_path
                : url('storage/' . $member->image_path);
        } else {
            $memberArray['image_path'] = null;
        }

        // Maintain the 'image' alias compatibility
        $memberArray['image'] = $memberArray['image_path'];

        // Determine the grouping key safely
        $memberArray['group_state'] = $member->state ?: 'National';

        return $memberArray;
    });

    // 3. Group the entire collection state-wise
    $groupedData = $formattedMembers->groupBy('group_state');

    return response()->json([
        'status' => 'success',
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

        // Add full image URL to each member
        $familyMembers->transform(function ($member) {
            if ($member->image) {
                $member->image = url($member->image);
            }
            return $member;
        });

        return response()->json([
            'status' => 'success',
            'data' => $familyMembers
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

        // Add full image URL
        if ($familyMember->image) {
            $familyMember->image = url($familyMember->image);
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
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/family_members'), $imageName);
            $validatedData['image'] = 'uploads/family_members/' . $imageName;
        }

        $familyMember = new FamilyMember($validatedData);
        $familyMember->customer_id = $customer->id;
        $familyMember->save();

        // Convert to array and add full image URL
        $responseData = $familyMember->toArray();
        if (!empty($familyMember->image)) {
            $responseData['image'] = url($familyMember->image);
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
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/family_members'), $imageName);
            $validatedData['image'] = 'uploads/family_members/' . $imageName;
        }

        $familyMember->update($validatedData);

        // Convert to array and add full image URL
        $responseData = $familyMember->toArray();
        if (!empty($familyMember->image)) {
            $responseData['image'] = url($familyMember->image);
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

        // Get customers with today's birthday from the same admin
        $todayBirthdays = Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('date_of_birth')
            ->whereRaw('MONTH(date_of_birth) = ?', [Carbon::now()->month])
            ->whereRaw('DAY(date_of_birth) = ?', [Carbon::now()->day])
            ->select('id', 'name', 'mobile', 'whatsapp', 'date_of_birth', 'village_id', 'area')
            ->with('village:id,name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $todayBirthdays
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

        // Get customers with today's anniversary from the same admin
        $todayAnniversaries = Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('anniversary_date')
            ->whereRaw('MONTH(anniversary_date) = ?', [Carbon::now()->month])
            ->whereRaw('DAY(anniversary_date) = ?', [Carbon::now()->day])
            ->select('id', 'name', 'mobile', 'whatsapp', 'anniversary_date', 'village_id', 'area')
            ->with('village:id,name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $todayAnniversaries
        ]);
    }

    /**
     * Get all unique business names from customers
     */
    public function getBusinessCategories(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer || !$customer->admin_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid customer data.'
            ], 400);
        }

        // Get unique business types and transform them into objects
        $categories = Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('business_type')
            ->where('business_type', '!=', '')
            ->distinct()
            ->orderBy('business_type', 'asc')
            ->pluck('business_type')
            ->map(function ($type, $index) {
                return [
                    'id' => $index + 1, // Generate a temporary ID for the list
                    'category_name' => $type
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Get business names - Updated to filter by category object name
     * Call this as: api/customer/business-names?category=busines
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

        // Get the category from the request (sent when you click the list item)
        $category = $request->query('category');
        $search = $request->query('search');

        $query = Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('business_name')
            ->where('business_name', '!=', '');

        // IF CATEGORY IS CLICKED: filter the results
        if ($category) {
            $query->where('business_name', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $search . '%');
            });
        }

        $businessNames = $query->selectRaw('business_name, business_type, product_service, office_address, mobile, name, COUNT(*) as count')
            ->groupBy('business_name', 'business_type', 'product_service', 'office_address', 'mobile', 'name')
            ->orderBy('business_name', 'asc')
            ->get();

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

        $businessName = $request->query('business_name');

        if (!$businessName) {
            return response()->json([
                'status' => 'error',
                'message' => 'Business name is required.'
            ], 400);
        }

        $customers = Customer::where('admin_id', $customer->admin_id)
            ->where('business_name', $businessName)
            ->select('id', 'name', 'mobile', 'whatsapp', 'email', 'business_name', 'business_type', 'product_service', 'office_address', 'village_id')
            ->with('village:id,name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }

    /**
     * Get all customers grouped by business name
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

        $customers = Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('business_name')
            ->where('business_name', '!=', '')
            ->select('id', 'name', 'mobile', 'whatsapp', 'email', 'business_name', 'business_type', 'product_service', 'office_address', 'village_id')
            ->with('village:id,name')
            ->get();

        $result = $customers->groupBy('business_name')->map(function ($items, $key) {
            return [
                'business_name' => $key,
                'count' => $items->count(),
                'customers' => $items
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }

    /**
     * Get customers by specific business name (Route parameter)
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

        $customers = Customer::where('admin_id', $customer->admin_id)
            ->where('business_name', $business)
            ->select('id', 'name', 'mobile', 'whatsapp', 'email', 'business_name', 'business_type', 'product_service', 'office_address', 'village_id')
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
        $gender = $request->query('gender'); // New: 'male' or 'female'
        $min_age = $request->query('min_age');
        $max_age = $request->query('max_age');
        $from_dob = $request->query('from_dob');
        $to_dob = $request->query('to_dob');

        // Start Query
        $query = Customer::where('admin_id', $customer->admin_id)
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
            })
            ->with(['familyMembers' => function ($q) use ($gender, $min_age, $max_age, $from_dob, $to_dob) {
                $q->where('matrimony', true);
                if ($gender) $q->where('gender', $gender);
                if ($min_age) $q->where('date_of_birth', '<=', now()->subYears($min_age)->format('Y-m-d'));
                if ($max_age) $q->where('date_of_birth', '>=', now()->subYears($max_age + 1)->addDay()->format('Y-m-d'));
                if ($from_dob) $q->where('date_of_birth', '>=', $from_dob);
                if ($to_dob) $q->where('date_of_birth', '<=', $to_dob);
            }]);

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

        // Map results to your flat structure
        $result = [];
        foreach ($customers as $cust) {
            foreach ($cust->familyMembers as $fm) {
                $result[] = [
                    'id' => $cust->id,
                    'name' => $cust->name,
                    'father_name' => $cust->father_name,
                    'date_of_birth' => $cust->date_of_birth,
                    'education' => $cust->education,
                    'mobile' => $cust->mobile,
                    'customer_id' => $cust->id,
                    'family_member_name' => $fm->name,
                    'family_member_gender' => $fm->gender, // Included gender
                    'family_member_relationship' => $fm->relationship,
                    'family_member_education' => $fm->education,
                    'family_member_date_of_birth' => $fm->date_of_birth,
                    'family_member_mobile' => $fm->mobile,
                    'family_member_age' => $fm->date_of_birth ? \Carbon\Carbon::parse($fm->date_of_birth)->age : null,
                    'matrimony' => $fm->matrimony,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }
}
