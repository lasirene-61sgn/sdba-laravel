<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
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
        $banners = Banner::where('admin_id', $this->getAdminId())
                         ->orderBy('created_at', 'desc')
                         ->paginate(10); 
                                   
        return view('admin.banner.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banner.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // Max 5MB
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = $request->file('image')->store('banner_images', 'public');

        Banner::create([
            'admin_id' => $this->getAdminId(), // Assign the current admin's ID
            'image_path' => $imagePath,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.banner.index')->with('success', 'Banner created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        // Authorization check: Ensure admin can only edit their own banners
        if ($banner->admin_id !== $this->getAdminId()) {
            abort(403);
        }

        return view('admin.banner.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        // Authorization check: Ensure admin can only update their own banners
        if ($banner->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120', 
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = $banner->image_path;
        if ($request->hasFile('image')) {
            // Delete old image
            Storage::disk('public')->delete($banner->image_path);
            // Upload new image
            $imagePath = $request->file('image')->store('banner_images', 'public');
        }

        $banner->update([
            'image_path' => $imagePath,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.banner.index')->with('success', 'Banner updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        // Authorization check
        if ($banner->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        
        return redirect()->route('admin.banner.index')->with('success', 'Banner deleted successfully.');
    }
}