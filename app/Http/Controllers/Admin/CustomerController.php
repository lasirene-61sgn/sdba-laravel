<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Ai\Ai;

class CustomerController extends Controller
{
    /**
     * Helper to get the ID of the currently authenticated admin.
     */
    private function getCurrentAdminId()
    {
        return Auth::guard('admin')->id();
    }

    /**
     * Get field permissions for the current admin user
     */
    private function getFieldPermissions()
    {
        return Auth::guard('admin')->user()->customer_field_permissions ?? [];
    }

    /**
     * Display a listing of the resource (Index).
     */
    public function index()
    {
        $adminId = $this->getCurrentAdminId();

        // CRITICAL FIX: Only fetch customers created by the current admin (paginated)
        // Sort by ID ascending to show oldest customers first (order of creation)
        $customers = Customer::with('village')
            ->where('admin_id', $adminId)
            ->orderBy('id', 'asc')
            ->paginate(10); // Show 10 customers per page

        // Get all customers for printing (without pagination)
        // Limit to 5000 records to balance usability with performance
        // Sort by ID ascending to show oldest customers first (order of creation)
        $allCustomers = Customer::with('village')
            ->where('admin_id', $adminId)
            ->orderBy('id', 'asc')
            ->limit(5000)
            ->get();

        // Get field permissions
        $fieldPermissions = $this->getFieldPermissions();

        return view('admin.customer.index', compact('customers', 'fieldPermissions', 'allCustomers'));
    }

    /**
     * Show the form for creating a new resource (Create).
     */
    public function create()
    {
        $adminId = $this->getCurrentAdminId();

        // Fetch only villages created by the current admin (Data Isolation)
        $villages = Village::where('admin_id', $adminId)->pluck('name', 'id');

        // Get field permissions
        $fieldPermissions = $this->getFieldPermissions();

        return view('admin.customer.create', compact('villages', 'fieldPermissions'));
    }

    /**
     * Store a newly created resource in storage (Store).
     */
    public function store(Request $request)
    {
        // 1. Validate customer details + family arrays
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'father_name' => 'nullable|string|max:100',
            'gotra' => 'nullable|string|max:100',
            'label_name' => 'nullable|string|max:100',
            'village_id' => 'nullable|exists:villages,id',
            'district' => 'nullable|string|max:100',
            'ms_firm_name' => 'nullable|string|max:100',
            'dno' => 'nullable|string|max:50',
            'street_road' => 'nullable|string|max:150',
            'address2' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:20',
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
            'education' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'hobbies' => 'nullable|string|max:255',
            'native_place' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',

            // Family validation rules
            'family' => 'nullable|array',
            'family.*.name' => 'required_with:family|string|max:100',
            'family.*.relationship' => 'nullable|string|max:100',
            'family.*.gender' => 'nullable|in:male,female,other',
            'family.*.mobile' => 'nullable|string|max:20',
            'family.*.date_of_birth' => 'nullable|date',
            'family.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Safety check: Ensure the selected village belongs to the current admin
        if ($request->filled('village_id')) {
            $village = Village::find($request->village_id);
            if (!$village || $village->admin_id !== $this->getCurrentAdminId()) {
                return redirect()->back()->withInput()->withErrors(['village_id' => 'Invalid village selection.']);
            }
        }

        // Handle customer image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('customer_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        if($request->hasFile('background_image')){
            $imagePath = $request->file('background_image')->store('customer_backgrounds', 'public');
            $validatedData['background_image'] = $imagePath;
        }

        // 2. Create the main customer record
        $customer = Customer::create(array_merge($validatedData, [
            'admin_id' => $this->getCurrentAdminId(),
        ]));

        // 3. Process and create family members if any are provided
        if ($request->has('family') && is_array($request->family)) {
            foreach ($request->family as $index => $familyData) {
                // Check if a dedicated profile picture was uploaded for this family index item
                if ($request->hasFile("family.{$index}.image")) {
                    $familyImage = $request->file("family.{$index}.image");
                    $imageName = time() . '_' . $index . '.' . $familyImage->extension();
                    $familyImage->move(public_path('uploads/family_members'), $imageName);
                    $familyData['image'] = 'uploads/family_members/' . $imageName;
                }

                // Save relationship array attached directly to the new customer ID
                $customer->familyMembers()->create($familyData);
            }
        }

        return redirect()->route('admin.customer.index')->with('success', 'Customer and family profile created successfully!');
    }

    /**
     * Display the specified resource (Show).
     */
    public function show(Customer $customer)
    {
        $adminId = $this->getCurrentAdminId();

        // CRITICAL FIX: Enforce ownership check before showing
        if ($customer->admin_id !== $adminId) {
            abort(403, 'Unauthorized access: You can only view customers you created.');
        }

        // Load the village relationship
        $customer->load('village');

        // Get field permissions
        $fieldPermissions = $this->getFieldPermissions();

        return view('admin.customer.show', compact('customer', 'fieldPermissions'));
    }

    /**
     * Show the form for editing the specified resource (Edit).
     */
    public function edit(Customer $customer)
    {
        $adminId = $this->getCurrentAdminId();

        // CRITICAL FIX: Enforce ownership check before editing
        if ($customer->admin_id !== $adminId) {
            abort(403, 'Unauthorized access: You can only edit customers you created.');
        }

        // 1. Get the IDs of villages created by the current admin.
        $allowedVillageIds = Village::where('admin_id', $adminId)->pluck('id')->toArray();

        // 2. Include the customer's currently saved village_id 
        if ($customer->village_id) {
            $allowedVillageIds[] = $customer->village_id;
            $allowedVillageIds = array_unique($allowedVillageIds);
        }

        // 3. Fetch the required villages using the collected IDs.
        $villages = Village::whereIn('id', $allowedVillageIds)->pluck('name', 'id');

        // Get field permissions
        $fieldPermissions = $this->getFieldPermissions();

        return view('admin.customer.edit', compact('customer', 'villages', 'fieldPermissions'));
    }

    /**
     * Update the specified resource in storage (Update).
     */
    public function update(Request $request, Customer $customer)
    {
        $adminId = $this->getCurrentAdminId();

        // CRITICAL FIX: Enforce ownership check before updating
        if ($customer->admin_id !== $adminId) {
            abort(403, 'Unauthorized action: You can only update customers you created.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'father_name' => 'nullable|string|max:100',
            'gotra' => 'nullable|string|max:100',
            'label_name' => 'nullable|string|max:100',
            'village_id' => 'nullable|exists:villages,id',
            'district' => 'nullable|string|max:100',
            'ms_firm_name' => 'nullable|string|max:100',
            'dno' => 'nullable|string|max:50',
            'street_road' => 'nullable|string|max:150',
            'address2' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:20',
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
            'education' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'hobbies' => 'nullable|string|max:255',
            'native_place' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        // Safety check: Ensure the selected village is either owned by the admin or the existing saved one
        if ($request->filled('village_id')) {
            $village = Village::find($request->village_id);
            if (!$village || ($village->admin_id !== $adminId && $village->id !== $customer->village_id)) {
                return redirect()->back()->withInput()->withErrors(['village_id' => 'Invalid village selection or permission denied.']);
            }
        }

        if($request->hasFile('background_image')) {
            if($customer->background_image && Storage::exists('public/' . $customer->background_image)){
                Storage::delete('public/' . $customer->background_image);
            }
            $imagePath = $request->file('background_image')->store('customer_backgrounds', 'public');
            $validatedData['background_image'] = $imagePath;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($customer->image && Storage::exists('public/' . $customer->image)) {
                Storage::delete('public/' . $customer->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('customer_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $customer->update($validatedData);

        return redirect()->route('admin.customer.index')->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified resource from storage (Destroy).
     */
    public function destroy(Customer $customer)
    {
        $adminId = $this->getCurrentAdminId();

        // CRITICAL FIX: Enforce ownership check before deletion
        if ($customer->admin_id !== $adminId) {
            abort(403, 'Unauthorized action: You can only delete customers you created.');
        }

        $customer->delete();
        return redirect()->route('admin.customer.index')->with('success', 'Customer deleted successfully!');
    }

    /**
     * Show the bulk upload form
     */
    public function showBulkUploadForm()
    {
        // Get field permissions
        $fieldPermissions = $this->getFieldPermissions();

        // Get existing villages for the current admin
        $adminId = $this->getCurrentAdminId();
        $villages = Village::where('admin_id', $adminId)->pluck('name', 'id');

        return view('admin.customer.bulk-upload', compact('fieldPermissions', 'villages'));
    }

    /**
     * Handle bulk upload of customers from CSV file
     */
    public function bulkUpload(Request $request)
    {
        $adminId = $this->getCurrentAdminId();

        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('excel_file');
            $handle = fopen($file->getPathname(), 'r');

            // Skip or read the header row
            $header = fgetcsv($handle);

            $successCount = 0;
            $errors = [];
            $rowIndex = 1;

            while (($row = fgetcsv($handle)) !== FALSE) {
                $rowIndex++;

                if (empty(array_filter($row))) {
                    continue;
                }

                // Clean and trim all fields safely
                $row = array_map('trim', $row);

                // FIX: Correct sequential mapping of array indices 0 through 28
                $customerData = [
                    'name'             => $row[0] ?? '',
                    'father_name'      => $row[1] ?? null,
                    'gotra'            => $row[2] ?? null,
                    'label_name'       => $row[3] ?? null,
                    'district'         => $row[4] ?? null,
                    'ms_firm_name'     => $row[5] ?? null,
                    'dno'              => $row[6] ?? null,
                    'street_road'      => $row[7] ?? null,
                    'address2'         => $row[8] ?? null,
                    'city'             => $row[9] ?? null,
                    'pincode'          => $row[10] ?? null,
                    'mobile'           => $row[11] ?? null,
                    'whatsapp'         => $row[12] ?? null,
                    'email'            => $row[13] ?? null,
                    'age'              => isset($row[14]) && $row[14] !== '' ? (int)$row[14] : null,
                    'gender'           => strtolower($row[15] ?? ''),
                    'business_type'    => $row[16] ?? null,
                    'business_name'    => $row[17] ?? null,
                    'product_service'  => $row[18] ?? null,
                    'office_address'   => $row[19] ?? null,
                    'date_of_birth'    => !empty($row[20]) ? date('Y-m-d', strtotime($row[20])) : null,
                    'anniversary_date' => !empty($row[21]) ? date('Y-m-d', strtotime($row[21])) : null,
                    'education'        => $row[22] ?? null,
                    'occupation'       => $row[23] ?? null,
                    'blood_group'      => $row[24] ?? null,
                    'hobbies'          => $row[25] ?? null,
                    'native_place'     => $row[26] ?? null,
                    'status'           => !empty($row[27]) ? strtolower($row[27]) : 'active',
                    'area'             => $row[28] ?? null,
                    'state'            => $row[29] ?? null, // Index 29 if your sheet has 30 columns total
                ];

                // FIX: Dynamic fallback logic to find where Village is stored in your sheet rows
                // Based on old logic, if 'city' (index 9) or a custom column contains village data:
                $villageName = $row[9] ?? null;

                if (!empty($villageName)) {
                    $village = Village::where('name', $villageName)->where('admin_id', $adminId)->first();

                    if (!$village) {
                        $village = Village::create([
                            'name'     => $villageName,
                            'admin_id' => $adminId
                        ]);
                    }

                    $customerData['village_id'] = $village->id;
                }

                // Validate the data structure
                $validator = Validator::make($customerData, [
                    'name'             => 'required|string|max:100',
                    'father_name'      => 'nullable|string|max:100',
                    'gotra'            => 'nullable|string|max:100',
                    'label_name'       => 'nullable|string|max:100',
                    'district'         => 'nullable|string|max:100',
                    'ms_firm_name'     => 'nullable|string|max:100',
                    'dno'              => 'nullable|string|max:50',
                    'street_road'      => 'nullable|string|max:150',
                    'address2'         => 'nullable|string|max:150',
                    'city'             => 'nullable|string|max:100',
                    'pincode'          => 'nullable|string|max:10',
                    'mobile'           => 'nullable|string|max:20',
                    'whatsapp'         => 'nullable|string|max:20',
                    'email'            => 'nullable|email|max:100',
                    'age'              => 'nullable|integer|min:0|max:150',
                    'gender'           => 'nullable|string',
                    'business_type'    => 'nullable|string|max:100',
                    'business_name'    => 'nullable|string|max:100',
                    'product_service'  => 'nullable|string|max:100',
                    'office_address'   => 'nullable|string|max:500',
                    'date_of_birth'    => 'nullable|date',
                    'anniversary_date' => 'nullable|date',
                    'education'        => 'nullable|string|max:100',
                    'occupation'       => 'nullable|string|max:100',
                    'blood_group'      => 'nullable|string|max:10',
                    'hobbies'          => 'nullable|string|max:255',
                    'native_place'     => 'nullable|string|max:100',
                    'status'           => 'required|in:active,inactive',
                    'area'             => 'nullable|string|max:100',
                    'state'            => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Row " . $rowIndex . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $customerData['admin_id'] = $adminId;

                Customer::create($customerData);
                $successCount++;
            }

            fclose($handle);

            if (!empty($errors)) {
                return redirect()->back()->with('errors', $errors)->with('success_count', $successCount);
            }

            return redirect()->route('admin.customer.index')->with('success', "$successCount customers imported successfully!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing customers: ' . $e->getMessage());
        }
    }

    /**
     * Process document slips asynchronously using modern Laravel AI architecture.
     */
    /**
     * Process document slips asynchronously using modern Laravel AI architecture.
     */
    /**
     * Process document slips asynchronously using native Gemini API transport pipelines.
     */
    public function scanCard(Request $request)
    {
        if (!$request->hasFile('scanned_image')) {
            return response()->json(['success' => false, 'message' => 'No image file uploaded.']);
        }

        try {
            $file = $request->file('scanned_image');

            // 1. Grab your API Key safely from your environment file
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                throw new \Exception("GEMINI_API_KEY is not defined inside your local .env file setup.");
            }

            // 2. Prepare the Image Data structure for Gemini payload standards
            $imageData = base64_encode(file_get_contents($file->getPathname()));
            $mimeType = $file->getMimeType();

            // 3. Set up clear text instructions specifying the JSON structure explicitly
            $systemInstruction = "You are an elite data entry clerk. Carefully read the handwritten or typed text inside the attached registration form slip image.\n" .
                "Extract the values exactly as they are written into a raw JSON format structure containing only these precise keys:\n" .
                "{\n" .
                "  \"name\": \"Full Name\",\n" .
                "  \"father_name\": \"Father Name\",\n" .
                "  \"gotra\": \"Gotra text\",\n" .
                "  \"label_name\": null,\n" .
                "  \"city\": \"City Name\",\n" .
                "  \"pincode\": \"Pincode\",\n" .
                "  \"mobile\": \"First mobile number listed\",\n" .
                "  \"address2\": \"Residence address\",\n" .
                "  \"office_address\": \"Firm Name and Address combined\"\n" .
                "}\n\n" .
                "STRICT RULES:\n" .
                "1. Respond ONLY with a clean, raw JSON object string. Do not include markdown ticks like ```json.\n" .
                "2. Translate or transliterate any native handwriting text into UPPERCASE ENGLISH CHARACTERS.";

            // 4. Construct the standard payload for the official Google Gemini 1.5 Flash Vision Endpoint
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemInstruction],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data'     => $imageData
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            // 5. Fire a direct, lightweight HTTP request, bypassing all local package vendor code
            $client = new \GuzzleHttp\Client();
            // REMOVED the appended query key string to fix cURL port translation failures
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent";
            $response = $client->post($url, [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Extract the text response from Google's standard array hierarchy
            $aiRawText = $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Clean up any accidental markdown tags appended by the model text stream
            $cleanJsonString = trim($aiRawText);
            $cleanJsonString = preg_replace('/^```json\s*/i', '', $cleanJsonString);
            $cleanJsonString = preg_replace('/```$/', '', $cleanJsonString);
            $cleanJsonString = trim($cleanJsonString);

            $extractedData = json_decode($cleanJsonString, true);

            if (!$extractedData) {
                throw new \Exception("Gemini returned invalid layout formatting data parameters: " . $aiRawText);
            }

            return response()->json([
                'success' => true,
                'data'    => $extractedData
            ]);
        } catch (\Exception $e) {
            // This will now catch the exact error description and send it cleanly to your browser screen!
            return response()->json([
                'success' => false,
                'message' => 'AI Mapping breakdown failure: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the custom layout workspace for AI screen onboarding.
     */
    public function scanCreateForm()
    {
        return view('admin.customer.scan-create');
    }

    public function indexFamilyMember(Customer $customer)
    {
        // Ensure the admin owns this customer
        if ($customer->admin_id !== $this->getCurrentAdminId()) {
            abort(403, 'Unauthorized action.');
        }

        $familyMembers = $customer->familyMembers;

        return view('admin.customer.family_members.index', compact('customer', 'familyMembers'));
    }

    /**
     * Show the form for creating a new family member.
     */
    public function createFamilyMember(Customer $customer)
    {
        if ($customer->admin_id !== $this->getCurrentAdminId()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.customer.family_members.create', compact('customer'));
    }

    /**
     * Store a newly created family member in storage.
     */
    public function storeFamilyMember(Request $request, Customer $customer)
    {
        if ($customer->admin_id !== $this->getCurrentAdminId()) {
            abort(403, 'Unauthorized action.');
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

        $customer->familyMembers()->create($validatedData);

        return redirect()->route('admin.customer.family.index', $customer->id)
            ->with('success', 'Family member added successfully!');
    }

    /**
     * Show the form for editing the specified family member.
     */
    public function editFamilyMember(Customer $customer, $familyMemberId)
    {
        if ($customer->admin_id !== $this->getCurrentAdminId()) {
            abort(403, 'Unauthorized action.');
        }

        $familyMember = $customer->familyMembers()->findOrFail($familyMemberId);

        return view('admin.customer.family_members.edit', compact('customer', 'familyMember'));
    }

    /**
     * Update the specified family member in storage.
     */
    public function updateFamilyMember(Request $request, Customer $customer, $familyMemberId)
    {
        if ($customer->admin_id !== $this->getCurrentAdminId()) {
            abort(403, 'Unauthorized action.');
        }

        $familyMember = $customer->familyMembers()->findOrFail($familyMemberId);

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
            if ($familyMember->image && File::exists(public_path($familyMember->image))) {
                File::delete(public_path($familyMember->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/family_members'), $imageName);
            $validatedData['image'] = 'uploads/family_members/' . $imageName;
        }

        $familyMember->update($validatedData);

        return redirect()->route('admin.customer.family.index', $customer->id)
            ->with('success', 'Family member updated successfully!');
    }

    /**
     * Remove the specified family member from storage.
     */
    public function deleteFamilyMember(Customer $customer, $familyMemberId)
    {
        if ($customer->admin_id !== $this->getCurrentAdminId()) {
            abort(403, 'Unauthorized action.');
        }

        $familyMember = $customer->familyMembers()->findOrFail($familyMemberId);

        if ($familyMember->image && File::exists(public_path($familyMember->image))) {
            File::delete(public_path($familyMember->image));
        }

        $familyMember->delete();

        return redirect()->route('admin.customer.family.index', $customer->id)
            ->with('success', 'Family member deleted successfully!');
    }
}
