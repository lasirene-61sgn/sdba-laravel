<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\PollResponse;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $polls = Poll::where('admin_id', Auth::guard('admin')->id())
            ->withCount('responses')
            ->latest()
            ->paginate(10);
            
        return view('admin.polls.index', compact('polls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.polls.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        Poll::create([
            'admin_id' => Auth::guard('admin')->id(),
            'description' => $request->description,
            'active' => true,
        ]);

        return redirect()->route('admin.polls.index')
            ->with('success', 'Poll created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $poll = Poll::where('admin_id', Auth::guard('admin')->id())
            ->with('responses.customer')
            ->findOrFail($id);
            
        return view('admin.polls.show', compact('poll'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $poll = Poll::where('admin_id', Auth::guard('admin')->id())
            ->findOrFail($id);
            
        return view('admin.polls.edit', compact('poll'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $poll = Poll::where('admin_id', Auth::guard('admin')->id())
            ->findOrFail($id);
            
        $request->validate([
            'description' => 'required|string|max:1000',
            'active' => 'boolean',
        ]);

        $poll->update([
            'description' => $request->description,
            'active' => $request->active ?? false,
        ]);

        return redirect()->route('admin.polls.index')
            ->with('success', 'Poll updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $poll = Poll::where('admin_id', Auth::guard('admin')->id())
            ->findOrFail($id);
            
        $poll->delete();

        return redirect()->route('admin.polls.index')
            ->with('success', 'Poll deleted successfully.');
    }
    
    /**
     * Toggle poll active status.
     */
    public function toggleActive(Request $request, string $id)
    {
        $poll = Poll::where('admin_id', Auth::guard('admin')->id())
            ->findOrFail($id);
            
        $poll->update(['active' => !$poll->active]);

        return redirect()->back()
            ->with('success', 'Poll status updated successfully.');
    }
}