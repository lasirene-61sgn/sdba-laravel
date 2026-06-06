<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Village;

class VillageController extends Controller
{
    /**
     * Display a listing of villages
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $villages = Village::where('admin_id', $adminId)
                          ->latest()
                          ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $villages,
        ]);
    }

    /**
     * Store a newly created village
     */
    public function store(Request $request)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $village = Village::create(array_merge($validatedData, [
            'admin_id' => $adminId,
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Village created successfully!',
            'data' => $village,
        ], 201);
    }

    /**
     * Display the specified village
     */
    public function show(Request $request, Village $village)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($village->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access: You can only view villages you created.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $village,
        ]);
    }

    /**
     * Update the specified village
     */
    public function update(Request $request, Village $village)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($village->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only update villages you created.',
            ], 403);
        }
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $village->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Village updated successfully!',
            'data' => $village,
        ]);
    }

    /**
     * Remove the specified village
     */
    public function destroy(Request $request, Village $village)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($village->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only delete villages you created.',
            ], 403);
        }
        
        $village->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Village deleted successfully!',
        ]);
    }
}