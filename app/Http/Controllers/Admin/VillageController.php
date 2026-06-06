<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VillageController extends Controller
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
        $villages = Village::where('admin_id', $this->getAdminId())
                         ->orderBy('created_at', 'desc')
                         ->paginate(10); 
                                   
        return view('admin.village.index', compact('villages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.village.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200|nullable:villages,name',
            'status' => 'required|in:active,inactive',
        ]);

        Village::create([
            'admin_id' => $this->getAdminId(), // Assign the current admin's ID
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.village.index')->with('success', 'Village created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Village $village)
    {
        // Authorization check: Ensure admin can only edit their own entries
        if ($village->admin_id !== $this->getAdminId()) {
            abort(403);
        }

        return view('admin.village.edit', compact('village'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Village $village)
    {
        // Authorization check
        if ($village->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:200|unique:villages,name,' . $village->id,
            'status' => 'required|in:active,inactive',
        ]);

        $village->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.village.index')->with('success', 'Village updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Village $village)
    {
        // Authorization check
        if ($village->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        $village->delete();
        
        return redirect()->route('admin.village.index')->with('success', 'Village deleted successfully.');
    }
}