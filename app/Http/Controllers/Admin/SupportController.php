<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Support; // Renamed Model
use App\Models\SupportType; // Renamed Model
use App\Models\SupportCategory; // Renamed Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        
        // Get the selected category ID from the request
        $categoryId = $request->get('category_id');
        
        // Build the query for support entries
        $query = Support::with(['supportType', 'supportCategory'])
            ->where('admin_id', $adminId);
            
        // Apply category filter if selected
        if ($categoryId) {
            $query->where('support_category_id', $categoryId);
        }
        
        // Order by ID ascending and paginate
        $supports = $query->orderBy('id', 'asc')->paginate(10);
        
        // Get all support categories for the current admin (for filter dropdown)
        $supportCategories = SupportCategory::where('admin_id', $adminId)
            ->orderBy('name', 'asc')
            ->get();
        
        return view('admin.supports.index', compact('supports', 'supportCategories', 'categoryId'));
    }

    public function create()
    {
        // Get only support types and categories created by the current admin
        $supportTypes = SupportType::where('admin_id', Auth::guard('admin')->id())->get();
        $supportCategories = SupportCategory::where('admin_id', Auth::guard('admin')->id())->get();
        return view('admin.supports.create', compact('supportTypes', 'supportCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'nullable|string|max:20',
            'support_type_id' => 'required|exists:support_types,id,admin_id,' . Auth::guard('admin')->id(),
            'support_category_id' => 'required|exists:support_categories,id,admin_id,' . Auth::guard('admin')->id(),
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Stores image in the 'public/supports' directory
            $imagePath = $request->file('image')->store('supports', 'public'); 
        }

        Support::create([
            'name' => $request->name,
            'image' => $imagePath,
            'phone' => $request->phone,
            'support_type_id' => $request->support_type_id,
            'support_category_id' => $request->support_category_id,
            'admin_id' => Auth::guard('admin')->id(), // Assign the current admin
        ]);

        return redirect()->route('admin.supports.index') 
                         ->with('success', 'Support entry created successfully!');
    }

    public function edit(Support $support) // Uses route model binding
    {
        // Ensure the support entry belongs to the current admin
        if ($support->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized access to support entry.');
        }
        
        $supportTypes = SupportType::where('admin_id', Auth::guard('admin')->id())->get();
        $supportCategories = SupportCategory::where('admin_id', Auth::guard('admin')->id())->get();
        return view('admin.supports.edit', compact('support', 'supportTypes', 'supportCategories'));
    }

    public function update(Request $request, Support $support)
    {
        // Ensure the support entry belongs to the current admin
        if ($support->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized access to support entry.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'nullable|string|max:20',
            'support_type_id' => 'required|exists:support_types,id,admin_id,' . Auth::guard('admin')->id(),
            'support_category_id' => 'required|exists:support_categories,id,admin_id,' . Auth::guard('admin')->id(),
        ]);

        $data = $request->except(['_token', '_method', 'image']);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($support->image) {
                Storage::disk('public')->delete($support->image);
            }
            // Store new image
            $imagePath = $request->file('image')->store('supports', 'public');
            $data['image'] = $imagePath;
        }
        
        $support->update($data);

        return redirect()->route('admin.supports.index') 
                         ->with('success', 'Support entry updated successfully!');
    }

    public function destroy(Support $support)
    {
        // Ensure the support entry belongs to the current admin
        if ($support->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized access to support entry.');
        }
        
        // Delete associated image file
        if ($support->image) {
            Storage::disk('public')->delete($support->image);
        }

        $support->delete();

        return redirect()->route('admin.supports.index')
                         ->with('success', 'Support entry deleted successfully!');
    }
}