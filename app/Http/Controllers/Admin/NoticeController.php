<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
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
        $notices = Notice::where('admin_id', $this->getAdminId())
                         ->orderBy('created_at', 'desc')
                         ->paginate(10); 
                                   
        return view('admin.notice.index', compact('notices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.notice.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Notice::create([
            'admin_id' => $this->getAdminId(), // Assign the current admin's ID
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.notice.index')->with('success', 'Notice created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        // Authorization check: Ensure admin can only edit their own notices
        if ($notice->admin_id !== $this->getAdminId()) {
            abort(403);
        }

        return view('admin.notice.edit', compact('notice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notice $notice)
    {
        // Authorization check
        if ($notice->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $notice->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.notice.index')->with('success', 'Notice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice)
    {
        // Authorization check
        if ($notice->admin_id !== $this->getAdminId()) {
            abort(403);
        }
        
        $notice->delete();
        
        return redirect()->route('admin.notice.index')->with('success', 'Notice deleted successfully.');
    }
}