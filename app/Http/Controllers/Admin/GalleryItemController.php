<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\RealTimeNotificationService;
use App\Services\NotificationsService;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class GalleryItemController extends Controller
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
        $galleryItems = GalleryItem::where('admin_id', $this->getAdminId())
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10); 
                                   
        return view('admin.gallery.index', compact('galleryItems'));
    }

    // public function show(GalleryItem $galleryItem){
    //     return view('admin.gallery.show', compact('galleryItem'));
    // }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, RealTimeNotificationService $pushService)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'images' => 'required|array|min:1', // Require at least one image
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate each image
            'videos' => 'nullable|array',
            'videos.*' => 'mimes:mp4,mov,ogg,qt|max:20000',
            'status' => 'required|in:active,inactive',
        ]);

        // Store all uploaded images
        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('gallery_images', 'public');
            $imagePaths[] = $imagePath;
        }

        $videoPaths = [];
        if($request->has('videos')){
            foreach ($request->file('videos') as $video){
                $videoPaths[] = $video->store('gallery_videos', 'public');
            }
        }

        $adminId = $this->getAdminId();
        $gallery = GalleryItem::create([
            'admin_id' => $this->getAdminId(), // Assign the current admin's ID
            'title' => $request->title,
            'description' => $request->description,
            'image_paths' => $imagePaths, // Store array of image paths
            'video_paths' => $videoPaths,
            'status' => $request->status,
        ]);

        if($gallery->status == 'active'){
            // Use the NotificationsService to create both in-app and real-time notifications
            $notificationService = new NotificationsService($pushService);
            $notificationService->createGalleryAddedNotification($adminId, $gallery);
        }

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery Item(s) created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GalleryItem $galleryItem)
    {
        // Authorization check: Ensure admin can only edit their own items
        // if ($galleryItem->admin_id !== $this->getAdminId()) {
        //     abort(403);
        // }

        return view('admin.gallery.edit', compact('galleryItem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GalleryItem $galleryItem, NotificationsService $notificationService)
    {
        // Authorization check: Ensure admin can only update their own items
        // if ($galleryItem->admin_id !== $this->getAdminId()) {
        //     abort(403);
        // }
        
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'images' => 'nullable|array', // Images are optional for update
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate each image
            'videos' => 'nullable|array',
            'videos.*' => 'mimes:mp4,mov,ogg,qt|max:20000',
            'remove_images' => 'nullable|array',
            'remove_videos' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        $currentImages = $galleryItem->image_paths ?? [];
        $currentVideos = $galleryItem->video_paths ?? [];

        // Prepare update data
        // $updateData = [
        //     'title' => $request->title,
        //     'description' => $request->description,
        //     'status' => $request->status,
        // ];

        if($request->has('remove_images')){
            foreach($request->remove_images as $pathToRemove){
                Storage::disk('public')->delete($pathToRemove);
                $currentImages = array_values(array_diff($currentImages, [$pathToRemove]));
            }
        }

        if($request->has('remove_videos')){
            foreach($request->remove_videos as $pathToRemove){
                Storage::disk('public')->delete($pathToRemove);
                $currentVideos =array_values(array_diff($currentVideos, [$pathToRemove]));
            }
        }

        // Handle image uploads if new images are provided
        // if ($request->hasFile('images')) {
        //     // Delete old images
        //     foreach ($galleryItem->image_paths as $oldImagePath) {
        //         Storage::disk('public')->delete($oldImagePath);
        //     }
            
        //     // Store new images
        //     $imagePaths = [];
        //     foreach ($request->file('images') as $image) {
        //         $imagePath = $image->store('gallery_images', 'public');
        //         $imagePaths[] = $imagePath;
        //     }
            
        //     $updateData['image_paths'] = $imagePaths;
        // }

        if($request->hasFile('images')){
            foreach($request->file('images') as $image){
                $currentImages[] = $image->store('gallery_images', 'public');
            }
        }

        if($request->hasFile('videos')){
            foreach($request->file('videos') as $video){
                $currentVideos[] = $video->store('gallery_videos', 'public');
            }
        }

        $galleryItem->update([
            'title' => $request->title,
            'description' => $request->description,
            'image_paths' => $currentImages,
            'video_paths' => $currentVideos,
            'status' => $request->status,
        ]);
        
        // Create notifications for customers when gallery item is updated
        $notificationService->createGalleryAddedNotification($this->getAdminId(), $galleryItem);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GalleryItem $galleryItem)
    {
        // Authorization check
        // if ($galleryItem->admin_id !== $this->getAdminId()) {
        //     abort(403);
        // }
        
        // Delete all images
        // foreach ($galleryItem->image_paths as $imagePath) {
        //     Storage::disk('public')->delete($imagePath);
        // }

        if(!empty($galleryItem->image_paths)){
            foreach($galleryItem->image_paths as $imagePath){
                Storage::disk('public')->delete($imagePath);
            }
        }

        if(!empty($galleryItem->video_paths)){
            foreach($galleryItem->video_paths as $videoPath){
                Storage::disk('public')->delete($videoPath);
            }
        }
        
        $galleryItem->delete();
        
        return redirect()->route('admin.gallery.index')->with('success', 'Gallery Item deleted successfully.');
    }
    
    /**
     * Handle bulk delete request.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return back()->with('error', 'No items selected for deletion.');
        }

        // Get gallery items to delete
        $galleryItems = GalleryItem::whereIn('id', $ids)->get();

        // Delete associated images
        foreach ($galleryItems as $item) {
            foreach ($item->image_paths as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // Delete the items from database
        GalleryItem::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' item(s) deleted successfully.');
    }
}