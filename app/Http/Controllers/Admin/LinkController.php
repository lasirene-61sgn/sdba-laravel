<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $link = Link::where('admin_id', $admin->id)->first();
        return view('admin.links.index', compact('link'));
    }

    public function create()
    {
        return view('admin.links.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'whatsapp_link' => 'nullable',
            'facebook_link' => 'nullable',
            'email_link' => 'nullable',
            'twitter_link' => 'nullable',
            'instagram_link' => 'nullable',
            'linkedin_link'=> 'nullable',
            'youtube_link' => 'nullable',
        ]);
        $admin = Auth::guard('admin')->user();
        Link::create([
            'admin_id' => $admin->id,
            'whatsapp_link' =>$request->input('whatsapp_link'),
            'facebook_link' => $request->input('facebook_link'),
            'email_link' => $request->input('email_link'),
            'twitter_link' => $request->input('twitter_link'),
            'instagram_link' => $request->input('instagram_link'),
            'linkedin_link' => $request->input('linkedin_link'),
            'youtube_link' => $request->input('youtube_link'),
        ]);
        return redirect()->route('admin.links.index')->with('succes', 'created');
    }

    public function edit($id)
    {
        $link = Link::findOrFail($id);
        return view('admin.links.edit', compact('link'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'whatsapp_link' => 'nullable',
            'facebook_link' => 'nullable',
            'email_link' => 'nullable',
            'twitter_link' => 'nullable',
            'instagram_link' => 'nullable',
            'linkedin_link'=> 'nullable',
            'youtube_link' => 'nullable',
        ]);
        $link = Link::findOrFail($id);
        $link->update([
            'whatsapp_link' =>$request->input('whatsapp_link'),
            'facebook_link' => $request->input('facebook_link'),
            'email_link' => $request->input('email_link'),
            'twitter_link' => $request->input('twitter_link'),
            'instagram_link' => $request->input('instagram_link'),
            'linkedin_link' => $request->input('linkedin_link'),
            'youtube_link' => $request->input('youtube_link'),
        ]);
        return redirect()->route('admin.links.index')->with('success', 'updated');
    }

    public function destroy($id)
    {
        $link = Link::findOrFail($id);
        $link->delete();
        return redirect()->route('admin.links.index')->with('success', 'deleted');
    }
}
