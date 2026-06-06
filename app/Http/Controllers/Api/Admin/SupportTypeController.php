<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportType;

class SupportTypeController extends Controller
{
    /**
     * Display a listing of support types
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $supportTypes = SupportType::where('admin_id', $adminId)
                                  ->latest()
                                  ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $supportTypes,
        ]);
    }

    /**
     * Store a newly created support type
     */
    public function store(Request $request)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:support_types,name,NULL,id,admin_id,' . $adminId,
        ]);

        $supportType = SupportType::create(array_merge($validatedData, [
            'admin_id' => $adminId,
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Support Type created successfully!',
            'data' => $supportType,
        ], 201);
    }

    /**
     * Display the specified support type
     */
    public function show(Request $request, SupportType $supportType)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($supportType->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access: You can only view support types you created.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $supportType,
        ]);
    }

    /**
     * Update the specified support type
     */
    public function update(Request $request, SupportType $supportType)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($supportType->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only update support types you created.',
            ], 403);
        }
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:support_types,name,' . $supportType->id . ',id,admin_id,' . $adminId,
        ]);

        $supportType->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Support Type updated successfully!',
            'data' => $supportType,
        ]);
    }

    /**
     * Remove the specified support type
     */
    public function destroy(Request $request, SupportType $supportType)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($supportType->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only delete support types you created.',
            ], 403);
        }
        
        $supportType->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Support Type deleted successfully!',
        ]);
    }
}