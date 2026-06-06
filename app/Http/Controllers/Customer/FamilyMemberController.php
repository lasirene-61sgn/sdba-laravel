<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FamilyMemberController extends Controller
{
    /**
     * Display a listing of the family members.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $familyMembers = $customer->familyMembers()->get();
        return view('customer.family_members.index', compact('familyMembers'));
    }

    /**
     * Show the form for creating a new family member.
     */
    public function create()
    {
        return view('customer.family_members.create');
    }

    /**
     * Store a newly created family member in storage.
     */
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        
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
            'matrimony' => 'nullable|boolean',
            'gender' => 'nullable|string|in:male,female,other',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Store new image
            $imagePath = $request->file('image')->store('family_member_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $familyMember = new FamilyMember($validatedData);
        $familyMember->customer_id = $customer->id;
        $familyMember->save();

        return redirect()->route('customer.family.members.index')->with('success', 'Family member added successfully!');
    }

    /**
     * Display the specified family member.
     */
    public function show(FamilyMember $familyMember)
    {
        // Ensure the family member belongs to the authenticated customer
        if ($familyMember->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }
        
        return view('customer.family_members.show', compact('familyMember'));
    }

    /**
     * Show the form for editing the specified family member.
     */
    public function edit(FamilyMember $familyMember)
    {
        // Ensure the family member belongs to the authenticated customer
        if ($familyMember->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }
        
        return view('customer.family_members.edit', compact('familyMember'));
    }

    /**
     * Update the specified family member in storage.
     */
    public function update(Request $request, FamilyMember $familyMember)
    {
        // Ensure the family member belongs to the authenticated customer
        if ($familyMember->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
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
            'matrimony' => 'nullable|boolean',
            'gender' => 'nullable|string|in:male,female,other',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($familyMember->image) {
                Storage::disk('public')->delete($familyMember->image);
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('family_member_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $familyMember->update($validatedData);

        return redirect()->route('customer.family.members.index')->with('success', 'Family member updated successfully!');
    }

    /**
     * Remove the specified family member from storage.
     */
    public function destroy(FamilyMember $familyMember)
    {
        // Ensure the family member belongs to the authenticated customer
        if ($familyMember->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }
        
        $familyMember->delete();

        return redirect()->route('customer.family.members.index')->with('success', 'Family member deleted successfully!');
    }
}