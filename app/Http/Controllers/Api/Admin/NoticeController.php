<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notice;

class NoticeController extends Controller
{
    /**
     * Display a listing of notices
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $notices = Notice::where('admin_id', $adminId)
                        ->latest()
                        ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $notices,
        ]);
    }

    /**
     * Store a newly created notice
     */
    public function store(Request $request)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validatedData['admin_id'] = $adminId;
        $notice = Notice::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Notice created successfully',
            'data' => $notice,
        ], 201);
    }

    /**
     * Display the specified notice
     */
    public function show(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $notice = Notice::where('admin_id', $adminId)
                       ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $notice,
        ]);
    }

    /**
     * Update the specified notice
     */
    public function update(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $notice = Notice::where('admin_id', $adminId)
                       ->findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $notice->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Notice updated successfully',
            'data' => $notice,
        ]);
    }

    /**
     * Remove the specified notice
     */
    public function destroy(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $notice = Notice::where('admin_id', $adminId)
                       ->findOrFail($id);

        $notice->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notice deleted successfully',
        ]);
    }
}