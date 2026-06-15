<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;

class AdminManagementController extends Controller
{
    // Define available sidebar items
    private $availableSidebarItems = [
        'dashboard' => 'Dashboard',
        'gallery' => 'Gallery',
        'banner' => 'Banner',
        'notice' => 'Notice',
        'village' => 'Village',
        'event' => 'Event',
        'news' => 'News',
        'support' => 'Support',
        'committee' => 'Committee',
        'customer' => 'Customers',
        'customer_plan' => 'Customers Plans',
        'bills' => 'Bills',
        'about_us' => 'About Us',
        'polls' => 'Polls',
        'links' => 'Links',
    ];
    
    // Define available customer fields
    private $availableCustomerFields = [
        'name' => 'Name',
        'image' => 'Image',
        'father_name' => 'Father Name',
        'gotra' => 'Gotra',
        'label_name' => 'Label Name',
        'district' => 'District',
        'ms_firm_name' => 'MS/Firm Name',
        'dno' => 'DNO',
        'street_road' => 'Street/Road',
        'address2' => 'Address Line 2',
        'city' => 'City',
        'pincode' => 'Pincode',
        'mobile' => 'Mobile',
        'whatsapp' => 'WhatsApp',
        'email' => 'Email',
        'age' => 'Age',
        'gender' => 'Gender',
        'business_type' => 'Business Type',
        'business_name' => 'Business Name',
        'product_service' => 'Product/Service',
        'office_address' => 'Office Address',
        'date_of_birth' => 'Date of Birth',
        'anniversary_date' => 'Anniversary Date',
        'education' => 'Education',
        'occupation' => 'Occupation',
        'blood_group' => 'Blood Group',
        'hobbies' => 'Hobbies',
        'native_place' => 'Native Place',
        'status' => 'Status'
    ];
    
    // Define restricted customer fields (only accessible by superadmins)
    private $restrictedCustomerFields = [
        'email' => 'Email',
        'age' => 'Age',
        'gender' => 'Gender',
        'business_type' => 'Business Type',
        'business_name' => 'Business Name',
        'product_service' => 'Product/Service',
        'office_address' => 'Office Address',
        'education' => 'Education',
        'occupation' => 'Occupation',
        'blood_group' => 'Blood Group',
        'hobbies' => 'Hobbies',
        'native_place' => 'Native Place'
    ];
    
    /**
     * Filter out restricted fields for non-superadmins
     */
    private function filterCustomerFieldsForAdmin()
    {
        // Since this controller is only accessible by superadmins, all fields are available
        return $this->availableCustomerFields;
    }
    
    /**
     * Display a listing of the Admins. (Used by Super Admin)
     */
    public function index()
    {
        $admins = Admin::paginate(10); // Show 10 admins per page
        return view('superadmin.admin_management.index', compact('admins'));
    }

    /**
     * Show the form for creating a new Admin. (Used by Super Admin)
     */
    public function create()
    {
        $sidebarItems = $this->availableSidebarItems;
        // Only superadmins can see restricted fields
        $customerFields = $this->filterCustomerFieldsForAdmin();
        return view('superadmin.admin_management.create', compact('sidebarItems', 'customerFields'));
    }

    /**
     * Store a newly created Admin in storage. (Used by Super Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'company_name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'sidebar_permissions' => ['array'],
            'sidebar_permissions.*' => ['string', 'in:' . implode(',', array_keys($this->availableSidebarItems))],
            'customer_field_permissions' => ['array'],
            'customer_field_permissions.*' => ['string', 'in:' . implode(',', array_keys($this->availableCustomerFields))],
        ]);
        
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('admin_images', 'public');
        }
        
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'image' => $imagePath,
            'password' => Hash::make($request->password),
            'sidebar_permissions' => $request->sidebar_permissions ?? array_keys($this->availableSidebarItems), // Default to all permissions
            'customer_field_permissions' => $request->customer_field_permissions ?? array_keys($this->availableCustomerFields), // Default to all permissions
        ]);

        return redirect()->route('superadmin.admins.index')
                         ->with('status', 'New Admin ('.$admin->name.') created successfully.');
    }

    /**
     * Show the form for editing the specified Admin. (Used by Super Admin)
     */
    public function edit(Admin $admin)
    {
        $sidebarItems = $this->availableSidebarItems;
        // Only superadmins can see restricted fields
        $customerFields = $this->filterCustomerFieldsForAdmin();
        return view('superadmin.admin_management.edit', compact('admin', 'sidebarItems', 'customerFields'));
    }

    /**
     * Update the specified Admin in storage. (Used by Super Admin)
     */
    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,'.$admin->id],
            'company_name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'sidebar_permissions' => ['array'],
            'sidebar_permissions.*' => ['string', 'in:' . implode(',', array_keys($this->availableSidebarItems))],
            'customer_field_permissions' => ['array'],
            'customer_field_permissions.*' => ['string', 'in:' . implode(',', array_keys($this->availableCustomerFields))],
        ]);

        // Handle image upload
        $imagePath = $admin->image; // Keep existing image if no new one is uploaded
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($admin->image && Storage::exists('public/' . $admin->image)) {
                Storage::delete('public/' . $admin->image);
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('admin_images', 'public');
        }

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'image' => $imagePath,
        ]);

        // Update sidebar permissions
        $admin->update([
            'sidebar_permissions' => $request->sidebar_permissions ?? array_keys($this->availableSidebarItems),
        ]);
        
        // Update customer field permissions
        $admin->update([
            'customer_field_permissions' => $request->customer_field_permissions ?? array_keys($this->availableCustomerFields),
        ]);

        return redirect()->route('superadmin.admins.index')
                         ->with('status', 'Admin ('.$admin->name.') updated successfully.');
    }

    /**
     * Remove the specified Admin from storage. (Used by Super Admin)
     */
    public function destroy(Admin $admin)
    {
        $adminName = $admin->name;
        $admin->delete();

        return redirect()->route('superadmin.admins.index')
                         ->with('status', 'Admin ('.$adminName.') deleted successfully.');
    }
}