<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\PollResponse;
use Illuminate\Support\Facades\Auth;

class CustomerPollController extends Controller
{
    public function index()
    {
        $polls = Poll::where('admin_id', Auth::guard('customer')->user()->admin_id)
            ->where('active', true)
            ->with(['responses' => function($query) {
                $query->where('customer_id', Auth::guard('customer')->id());
            }])
            ->withCount(['responses as yes_count' => function($query) {
                $query->where('response', 'yes');
            }])
            ->withCount(['responses as no_count' => function($query) {
                $query->where('response', 'no');
            }])
            ->withCount(['responses as maybe_count' => function($query) {
                $query->where('response', 'maybe');
            }])
            ->withCount('responses as total_responses')
            ->latest()
            ->paginate(10);
            
        return view('customer.polls.index', compact('polls'));
    }
    
    public function vote(Request $request, Poll $poll)
    {
        // Validate the poll belongs to the customer's admin and is active
        if ($poll->admin_id != Auth::guard('customer')->user()->admin_id || !$poll->active) {
            return redirect()->back()->with('error', 'Invalid poll.');
        }
        
        // Validate the response
        $request->validate([
            'response' => 'required|in:yes,no,maybe',
        ]);
        
        // Check if customer has already voted
        $existingVote = PollResponse::where('poll_id', $poll->id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->first();
            
        if ($existingVote) {
            return redirect()->back()->with('error', 'You have already voted on this poll.');
        }
        
        // Save the response
        PollResponse::create([
            'poll_id' => $poll->id,
            'customer_id' => Auth::guard('customer')->id(),
            'response' => $request->response,
        ]);
        
        return redirect()->back()->with('success', 'Thank you for your vote!');
    }
}