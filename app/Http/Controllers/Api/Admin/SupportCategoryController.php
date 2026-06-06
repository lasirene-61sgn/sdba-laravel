<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportCategory;

class SupportCategoryController extends Controller
{
    /**
     * Display a listing of support categories
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $supportCategories = SupportCategory::where('admin_id', $adminId)
                                          ->latest()
                                          ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $supportCategories,
        ]);
    }

    /**
     * Store a newly created support category
     */
    public function store(Request $request)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:support_categories,name,NULL,id,admin_id,' . $adminId,
        ]);

        $supportCategory = SupportCategory::create(array_merge($validatedData, [
            'admin_id' => $adminId,
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Support Category created successfully!',
            'data' => $supportCategory,
        ], 201);
    }

    /**
     * Display the specified support category
     */
    public function show(Request $request, SupportCategory $supportCategory)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($supportCategory->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access: You can only view support categories you created.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $supportCategory,
        ]);
    }

    /**
     * Update the specified support category
     */
    public function update(Request $request, SupportCategory $supportCategory)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($supportCategory->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only update support categories you created.',
            ], 403);
        }
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:support_categories,name,' . $supportCategory->id . ',id,admin_id,' . $adminId,
        ]);

        $supportCategory->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Support Category updated successfully!',
            'data' => $supportCategory,
        ]);
    }

    /**
     * Remove the specified support category
     */
    public function destroy(Request $request, SupportCategory $supportCategory)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($supportCategory->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only delete support categories you created.',
            ], 403);
        }
        
        $supportCategory->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Support Category deleted successfully!',
        ]);
    }
}