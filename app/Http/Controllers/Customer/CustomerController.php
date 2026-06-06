<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display the customer dashboard
     */
    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get customer plans
        $plans = $customer->customerPlans()->get();
        
        // Get family members added by this customer
        $familyMembers = $customer->familyMembers()->get();
        
        // Get family members with matrimony status
        $matrimonyMembers = $customer->familyMembers()->where('matrimony', true)->get();
        
        // Get banners from the same admin
        $banners = \App\Models\Banner::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get upcoming events from the same admin
        $upcomingEvents = \App\Models\Event::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->where('posted_date', '>=', Carbon::today())
            ->orderBy('posted_date', 'asc')
            ->limit(3)
            ->get();

        // Get birthdays for the current month from both customers and family members
        $customerBirthdays = Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('date_of_birth')
            ->whereRaw('MONTH(date_of_birth) = ?', [Carbon::now()->month])
            ->orderByRaw('DAY(date_of_birth)')
            ->get();

        $familyBirthdays = \App\Models\FamilyMember::where('customer_id', $customer->id)
            ->whereNotNull('date_of_birth')
            ->whereRaw('MONTH(date_of_birth) = ?', [Carbon::now()->month])
            ->orderByRaw('DAY(date_of_birth)')
            ->get();

        // Combine and sort birthdays
        $birthdays = collect([]);
        foreach ($customerBirthdays as $birthday) {
            $birthdays->push([
                'name' => $birthday->name,
                'date' => $birthday->date_of_birth,
                'mobile' => $birthday->mobile,
                'whatsapp' => $birthday->whatsapp,
                'type' => 'customer'
            ]);
        }
        
        foreach ($familyBirthdays as $birthday) {
            $birthdays->push([
                'name' => $birthday->name,
                'date' => $birthday->date_of_birth,
                'mobile' => $birthday->customer->mobile, // Get mobile from associated customer
                'whatsapp' => $birthday->customer->whatsapp, // Get whatsapp from associated customer
                'type' => 'family',
                'relationship' => $birthday->relationship
            ]);
        }
        
        // Sort by day of birth
        $birthdays = $birthdays->sortBy(function($item) {
            return $item['date']->day;
        })->values();

        // Get anniversaries for the current month from both customers and family members
        $customerAnniversaries = Customer::where('admin_id', $customer->admin_id)
            ->whereNotNull('anniversary_date')
            ->whereRaw('MONTH(anniversary_date) = ?', [Carbon::now()->month])
            ->orderByRaw('DAY(anniversary_date)')
            ->get();

        $familyAnniversaries = \App\Models\FamilyMember::where('customer_id', $customer->id)
            ->whereNotNull('anniversary_date')
            ->whereRaw('MONTH(anniversary_date) = ?', [Carbon::now()->month])
            ->orderByRaw('DAY(anniversary_date)')
            ->get();

        // Combine and sort anniversaries
        $anniversaries = collect([]);
        foreach ($customerAnniversaries as $anniversary) {
            $anniversaries->push([
                'name' => $anniversary->name,
                'date' => $anniversary->anniversary_date,
                'mobile' => $anniversary->mobile,
                'whatsapp' => $anniversary->whatsapp,
                'type' => 'customer'
            ]);
        }
        
        foreach ($familyAnniversaries as $anniversary) {
            $anniversaries->push([
                'name' => $anniversary->name,
                'date' => $anniversary->anniversary_date,
                'mobile' => $anniversary->customer->mobile, // Get mobile from associated customer
                'whatsapp' => $anniversary->customer->whatsapp, // Get whatsapp from associated customer
                'type' => 'family',
                'relationship' => $anniversary->relationship
            ]);
        }
        
        // Sort by day of anniversary
        $anniversaries = $anniversaries->sortBy(function($item) {
            return $item['date']->day;
        })->values();

        // Get committee members from the same admin ordered by sort_order
        $committeeMembers = \App\Models\CommitteePerson::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->limit(6)
            ->get();
            
        // Get news items from the same admin
        $newsItems = \App\Models\News::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('posted_date', 'desc')
            ->limit(3)
            ->get();
        
        return view('customer.dashboard', compact(
            'customer', 
            'plans', 
            'familyMembers',
            'matrimonyMembers',
            'banners',
            'upcomingEvents',
            'birthdays',
            'anniversaries',
            'committeeMembers',
            'newsItems'
        ));
    }

    /**
     * Display the customer profile
     */
    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.profile', compact('customer'));
    }

    /**
     * Show the form for editing the customer profile
     */
    public function editProfile()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.edit_profile', compact('customer'));
    }

    /**
     * Update the customer profile
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        
        $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'father_name' => 'nullable|string|max:100',
            'gotra' => 'nullable|string|max:100',
            'label_name' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'ms_firm_name' => 'nullable|string|max:100',
            'dno' => 'nullable|string|max:50',
            'street_road' => 'nullable|string|max:150',
            'address2' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'anniversary_date' => 'nullable|date',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($customer->image && Storage::exists('public/' . $customer->image)) {
                Storage::delete('public/' . $customer->image);
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('customer_images', 'public');
            $customer->image = $imagePath;
        }

        // Update customer attributes
        $customer->name = $request->name;
        $customer->father_name = $request->father_name;
        $customer->gotra = $request->gotra;
        $customer->label_name = $request->label_name;
        $customer->district = $request->district;
        $customer->ms_firm_name = $request->ms_firm_name;
        $customer->dno = $request->dno;
        $customer->street_road = $request->street_road;
        $customer->address2 = $request->address2;
        $customer->city = $request->city;
        $customer->pincode = $request->pincode;
        $customer->whatsapp = $request->whatsapp;
        $customer->date_of_birth = $request->date_of_birth;
        $customer->anniversary_date = $request->anniversary_date;
        $customer->save();

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Display customer plans
     */
    public function plans()
    {
        $customer = Auth::guard('customer')->user();
        $plans = $customer->customerPlans()->get();
        return view('customer.plans', compact('plans'));
    }

    /**
     * Display a list of all customers from the same admin
     */
    public function listCustomers()
    {
        $customer = Auth::guard('customer')->user();
        
        // Log for debugging
        Log::info('Customer list request', [
            'current_customer_id' => $customer->id,
            'current_customer_admin_id' => $customer->admin_id ?? 'N/A'
        ]);
        
        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            Log::warning('Customer without admin_id attempted to view customer list', [
                'customer_id' => $customer->id
            ]);
            return redirect()->route('customer.dashboard')->with('error', 'Invalid customer data. Please contact administrator.');
        }
        
        // Get all customers from the same admin with all necessary relationships (paginated)
        $customers = Customer::with(['village'])
            ->where('admin_id', $customer->admin_id)
            ->where('id', '!=', $customer->id) // Exclude the current customer
            ->paginate(10); // Show 10 customers per page
        
        // Log the results
        Log::info('Customers retrieved for list', [
            'count' => $customers->count(),
            'admin_id' => $customer->admin_id
        ]);
        
        return view('customer.customers', compact('customers'));
    }
    
    /**
     * Display details of a specific customer
     */
    public function showCustomer($id)
    {
        $customer = Auth::guard('customer')->user();
        
        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            Log::warning('Customer without admin_id attempted to view customer details', [
                'customer_id' => $customer->id,
                'target_customer_id' => $id
            ]);
            return redirect()->route('customer.dashboard')->with('error', 'Invalid customer data. Please contact administrator.');
        }
        
        // Get the specific customer from the same admin
        $targetCustomer = Customer::with(['village'])
            ->where('id', $id)
            ->where('admin_id', $customer->admin_id)
            ->first();
        
        // Check if customer exists and belongs to the same admin
        if (!$targetCustomer) {
            Log::warning('Customer attempted to view non-existent or unauthorized customer', [
                'customer_id' => $customer->id,
                'target_customer_id' => $id
            ]);
            return redirect()->route('customer.list')->with('error', 'Customer not found or access denied.');
        }
        
        return view('customer.customer_detail', compact('targetCustomer'));
    }
    
    /**
     * Display the About Us page for the customer's admin
     */
    public function aboutUs()
    {
        $customer = Auth::guard('customer')->user();
        
        // Validate that the customer has an admin
        if (!$customer || !$customer->admin_id) {
            return redirect()->route('customer.dashboard')->with('error', 'Invalid customer data. Please contact administrator.');
        }
        
        // Get the About Us content for the customer's admin
        $aboutUs = $customer->admin->aboutUs;
        
        // If no About Us content exists, show a default message
        if (!$aboutUs) {
            $aboutUs = new \stdClass();
            $aboutUs->description = "No information available.";
            $aboutUs->vision = "No information available.";
            $aboutUs->mission = "No information available.";
            $aboutUs->image_path = null;
        }
        
        return view('customer.about-us', compact('aboutUs'));
    }
    
    /**
     * Display gallery items from the customer's admin
     */
    public function gallery()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get gallery items from the same admin
        $galleryItems = \App\Models\GalleryItem::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.gallery', compact('galleryItems'));
    }

    /**
     * Display a specific gallery item with all its images
     */
    public function showGalleryItem($id)
    {
        $customer = Auth::guard('customer')->user();
        
        // Get the specific gallery item from the same admin
        $galleryItem = \App\Models\GalleryItem::where('id', $id)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->firstOrFail();
        
        return view('customer.gallery_show', compact('galleryItem'));
    }

    /**
     * Display banner items from the customer's admin
     */
    public function banner()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get banner items from the same admin
        $banners = \App\Models\Banner::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.banner', compact('banners'));
    }
    
    /**
     * Display notice items from the customer's admin
     */
    public function notice()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get notice items from the same admin
        $notices = \App\Models\Notice::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.notice', compact('notices'));
    }
    
    /**
     * Display a specific notice item
     */
    public function showNoticeItem($id)
    {
        $customer = Auth::guard('customer')->user();
        
        // Get the specific notice item from the same admin
        $noticeItem = \App\Models\Notice::where('id', $id)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->firstOrFail();
        
        return view('customer.notice_show', compact('noticeItem'));
    }

    /**
     * Display village items from the customer's admin
     */
    public function village()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get village items from the same admin
        $villages = \App\Models\Village::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.village', compact('villages'));
    }
    
    /**
     * Display event items from the customer's admin
     */
    public function event()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get event items from the same admin with RSVP information
        $events = \App\Models\Event::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->with(['rsvps' => function($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.event', compact('events', 'customer'));
    }
    
    /**
     * Display news items from the customer's admin
     */
    public function news()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get news items from the same admin
        $newsItems = \App\Models\News::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('posted_date', 'desc')
            ->get();
        
        return view('customer.news', compact('newsItems'));
    }
    
    /**
     * Display support items from the customer's admin
     */
    public function support()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get support items from the same admin
        $supports = \App\Models\Support::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.support', compact('supports'));
    }
    
    /**
     * Display a specific support item
     */
    public function showSupportItem($id)
    {
        $customer = Auth::guard('customer')->user();
        
        // Get the specific support item from the same admin
        $supportItem = \App\Models\Support::where('id', $id)
            ->where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->firstOrFail();
        
        return view('customer.support_show', compact('supportItem'));
    }

    /**
     * Display committee items from the customer's admin
     */
    public function committee()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get committee items from the same admin ordered by sort_order
        $committeeMembers = \App\Models\CommitteePerson::where('admin_id', $customer->admin_id)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get();
        
        return view('customer.committee', compact('committeeMembers'));
    }
    
    /**
     * Display customer plan items for the specific customer
     */
    public function customerPlan()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get customer plan items for the specific customer
        $customerPlans = \App\Models\CustomerPlan::with('customer')
            ->where('customer_id', $customer->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(6); // Show 6 plans per page
        
        return view('customer.customer_plan', compact('customerPlans'));
    }
}