<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class SuperAdminAuthController extends Controller
{
    /**
     * Show the Super Admin login form.
     */
    public function showLoginForm()
    {
        // Points to the superadmin login blade file
        return view('superadmin.login');
    }

    /**
     * Handle the Super Admin login request using the dedicated 'superadmin' guard.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to log in using the 'superadmin' guard
        if (Auth::guard('superadmin')->attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();
            
            // Redirect to the Super Admin Dashboard
            return redirect()->intended(route('superadmin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our Super Admin records.',
        ])->onlyInput('email');
    }
    
    /**
     * Log the Super Admin user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login');
    }

    /**
     * Show the Super Admin profile form.
     */
    public function showProfile()
    {
        $superadmin = Auth::guard('superadmin')->user();
        return view('superadmin.profile', compact('superadmin'));
    }

    /**
     * Update the Super Admin profile.
     */
    public function updateProfile(Request $request)
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:super_admins,email,' . $superadmin->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($superadmin->image && Storage::exists('public/' . $superadmin->image)) {
                Storage::delete('public/' . $superadmin->image);
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('superadmin_images', 'public');
            $validatedData['image'] = $imagePath;
        } else {
            unset($validatedData['image']);
        }

        // Handle password update
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        foreach ($validatedData as $key => $value) {
            $superadmin->{$key} = $value;
        }
        $superadmin->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}