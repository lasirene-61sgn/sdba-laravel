<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Support;
use App\Models\SupportType;
use App\Models\SupportCategory;
use Illuminate\Support\Facades\Storage;

class SupportController extends Controller
{
    /**
     * Display a listing of supports
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $supports = Support::with(['supportType', 'supportCategory'])
                          ->where('admin_id', $adminId)
                          ->latest()
                          ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $supports,
        ]);
    }

    /**
     * Store a newly created support
     */
    public function store(Request $request)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'nullable|string|max:20',
            'support_type_id' => 'required|exists:support_types,id,admin_id,' . $adminId,
            'support_category_id' => 'required|exists:support_categories,id,admin_id,' . $adminId,
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('supports', 'public'); 
        }

        $support = Support::create(array_merge($validatedData, [
            'image' => $imagePath,
            'admin_id' => $adminId,
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Support entry created successfully!',
            'data' => $support,
        ], 201);
    }

    /**
     * Display the specified support
     */
    public function show(Request $request, Support $support)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($support->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access: You can only view support entries you created.',
            ], 403);
        }

        $support->load(['supportType', 'supportCategory']);

        return response()->json([
            'status' => 'success',
            'data' => $support,
        ]);
    }

    /**
     * Update the specified support
     */
    public function update(Request $request, Support $support)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($support->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only update support entries you created.',
            ], 403);
        }
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'nullable|string|max:20',
            'support_type_id' => 'required|exists:support_types,id,admin_id,' . $adminId,
            'support_category_id' => 'required|exists:support_categories,id,admin_id,' . $adminId,
        ]);

        $data = $request->except(['image']);

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

        return response()->json([
            'status' => 'success',
            'message' => 'Support entry updated successfully!',
            'data' => $support,
        ]);
    }

    /**
     * Remove the specified support
     */
    public function destroy(Request $request, Support $support)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($support->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only delete support entries you created.',
            ], 403);
        }
        
        // Delete associated image file
        if ($support->image) {
            Storage::disk('public')->delete($support->image);
        }
        
        $support->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Support entry deleted successfully!',
        ]);
    }
}