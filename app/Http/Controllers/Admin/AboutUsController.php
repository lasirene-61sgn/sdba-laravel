<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AboutUs;
use Illuminate\Support\Facades\Storage;

class AboutUsController extends Controller
{
    /**
     * Display the About Us form
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $aboutUs = $admin->aboutUs ?? new AboutUs();
        
        return view('admin.about-us.index', compact('aboutUs'));
    }

    /**
     * Update the About Us content
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'description' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $aboutUs = $admin->aboutUs ?? new AboutUs();
        $aboutUs->admin_id = $admin->id;
        $aboutUs->description = $request->description;
        $aboutUs->vision = $request->vision;
        $aboutUs->mission = $request->mission;
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($aboutUs->image_path && Storage::exists($aboutUs->image_path)) {
                Storage::delete($aboutUs->image_path);
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('about-us', 'public');
            $aboutUs->image_path = $imagePath;
        }
        
        $aboutUs->save();
        
        return redirect()->route('admin.about-us.index')
                         ->with('success', 'About Us content updated successfully!');
    }
}