<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Customer;
use App\Services\RealTimeNotificationService;
use App\Services\NotificationsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    // Get the ID of the currently logged-in Admin
    private function getAdminId()
    {
        // Adjust 'admin' to your guard name if different
        return Auth::guard('admin')->id(); 
    }

    /**
     * Display a listing of the resource (ONLY for the logged-in admin).
     */
    public function index()
    {
        $newsItems = News::where('admin_id', $this->getAdminId())
                         ->orderBy('posted_date', 'desc')
                         ->paginate(10); 
                                   
        return view('admin.news.index', compact('newsItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, RealTimeNotificationService $pushService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'keywords' => 'nullable|string',
            'summary' => 'nullable|string',
            'author' => 'required|string|max:100',
            'posted_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = $request->file('image')->store('news_images', 'public');

        $adminId = $this->getAdminId();
        $news = News::create([
            'admin_id' => $this->getAdminId(), // Assign the current admin's ID
            'title' => $request->title,
            'slug' => $request->slug, // Will be generated in Model if empty
            'image_path' => $imagePath,
            'keywords' => $request->keywords,
            'summary' => $request->summary,
            'author' => $request->author,
            'posted_date' => $request->posted_date,
            'status' => $request->status,
        ]);

        if($news->status == 'active'){
            // Use the NotificationsService to create both in-app and real-time notifications
            $notificationService = new NotificationsService($pushService);
            $notificationService->createNewsAddedNotification($adminId, $news);
        }

        return redirect()->route('admin.news.index')->with('success', 'News item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        // Authorization check: Ensure admin can only edit their own entries
        if ($news->admin_id !== $this->getAdminId()) {
            abort(403);
        }

        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news, NotificationsService $notificationService)
    {
        // Authorization check
        if ($news->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            // Unique slug validation, ignoring the current news item's ID
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $news->id, 
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'keywords' => 'nullable|string',
            'summary' => 'nullable|string',
            'author' => 'required|string|max:100',
            'posted_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = $news->image_path;
        if ($request->hasFile('image')) {
            // Delete old image
            Storage::disk('public')->delete($news->image_path);
            // Upload new image
            $imagePath = $request->file('image')->store('news_images', 'public');
        }

        $news->update([
            'title' => $request->title,
            'slug' => $request->slug, // Will be updated in Model if title changes
            'image_path' => $imagePath,
            'keywords' => $request->keywords,
            'summary' => $request->summary,
            'author' => $request->author,
            'posted_date' => $request->posted_date,
            'status' => $request->status,
        ]);
        
        // Create notifications for customers when news is updated
        $notificationService->createNewsAddedNotification($this->getAdminId(), $news);

        return redirect()->route('admin.news.index')->with('success', 'News item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        // Authorization check
        if ($news->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        Storage::disk('public')->delete($news->image_path);
        $news->delete();
        
        return redirect()->route('admin.news.index')->with('success', 'News item deleted successfully.');
    }
}