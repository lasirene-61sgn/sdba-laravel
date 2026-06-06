<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRSVP;

class EventRSVPController extends Controller
{
    public function index(Event $event)
    {
        // Get all RSVPs for this event with customer information
        $rsvps = EventRSVP::where('event_id', $event->id)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.event.rsvp_details', compact('event', 'rsvps'));
    }

    public function accepted(Event $event)
    {
        // Get accepted RSVPs for this event with customer information
        $rsvps = EventRSVP::where('event_id', $event->id)
            ->where('status', 'accepted')
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $title = 'Accepted';

        return view('admin.event.rsvp_reports', compact('event', 'rsvps', 'title'));
    }

    public function declined(Event $event)
    {
        // Get declined RSVPs for this event with customer information
        $rsvps = EventRSVP::where('event_id', $event->id)
            ->where('status', 'declined')
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $title = 'Declined';

        return view('admin.event.rsvp_reports', compact('event', 'rsvps', 'title'));
    }

    public function pending(Event $event)
    {
        // Get pending RSVPs for this event with customer information
        $rsvps = EventRSVP::where('event_id', $event->id)
            ->where('status', 'pending')
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $title = 'Pending';

        return view('admin.event.rsvp_reports', compact('event', 'rsvps', 'title'));
    }
}