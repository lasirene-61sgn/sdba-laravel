<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    /**
     * Display a listing of news
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $news = News::where('admin_id', $adminId)
                   ->latest()
                   ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $news,
        ]);
    }

    /**
     * Store a newly created news
     */
    public function store(Request $request, \App\Services\NotificationsService $notificationService)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'image_path' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'summary' => 'required|string',
            'author' => 'required|string|max:255',
            'posted_date' => 'required|date',
            'status' => 'required|in:draft,published,archived',
        ]);

        $validatedData['admin_id'] = $adminId;
        $news = News::create($validatedData);
        
        // Create notifications for customers
        $notificationService->createNewsAddedNotification($adminId, $news);

        return response()->json([
            'status' => 'success',
            'message' => 'News created successfully',
            'data' => $news,
        ], 201);
    }

    /**
     * Display the specified news
     */
    public function show(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $news = News::where('admin_id', $adminId)
                   ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $news,
        ]);
    }

    /**
     * Update the specified news
     */
    public function update(Request $request, $id, \App\Services\NotificationsService $notificationService)
    {
        $adminId = $request->user()->id;
        
        $news = News::where('admin_id', $adminId)
                   ->findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug,'.$id,
            'image_path' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'summary' => 'sometimes|string',
            'author' => 'sometimes|string|max:255',
            'posted_date' => 'sometimes|date',
            'status' => 'sometimes|in:draft,published,archived',
        ]);

        $news->update($validatedData);
        
        // Create notifications for customers when news is updated
        $notificationService->createNewsAddedNotification($adminId, $news);

        return response()->json([
            'status' => 'success',
            'message' => 'News updated successfully',
            'data' => $news,
        ]);
    }

    /**
     * Remove the specified news
     */
    public function destroy(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $news = News::where('admin_id', $adminId)
                   ->findOrFail($id);

        $news->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'News deleted successfully',
        ]);
    }
}