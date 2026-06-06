<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helpline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class HelplineController extends Controller
{
    private function getAdminId(){
        return Auth::guard('admin')->id();
    }

    public function index(){
        $helplines = Helpline::where('admin_id', $this->getAdminId())
            ->orderBy('name')
            ->orderBy('heading_name')
            ->get()
            ->groupBy('name');
        return view('admin.helpline.index', compact('helplines'));
    }

    public function create(){
        return view('admin.helpline.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'nullable',
            'heading_name' => 'nullable',
            'mobile_numbers' => 'nullable|array',
            'whatsapp_numbers' => 'nullable|array',
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email',
            'locations' => 'nullable|array',
        ]);
        $validated['admin_id'] = $this->getAdminId();
        Helpline::create($validated);
        return redirect()->route('admin.helpline.index')->with('success', 'Helpline Created Successfully');
    }

    public function edit(Helpline $helpline){
        if($helpline->admin_id !== $this->getAdminId()){
            abort(403);
        }
        return view('admin.helpline.edit', compact('helpline'));
    }

    public function update(Request $request, Helpline $helpline){
        if($helpline->admin_id !== $this->getAdminId()){
            abort(403);
        }
        $validated = $request->validate([
            'name' => 'nullable',
            'heading_name' => 'nullable',
            'mobile_numbers' => 'nullable|array',
            'whatsapp_numbers' => 'nullable|array',
            'emails' => 'nullable|array',
            'locations' => 'nullable|array',
        ]);
        $helpline->update($validated);
        return redirect()->route('admin.helpline.index')->with('success', 'Helpline updated Successfully');
    }

    public function destroy(Helpline $helpline){
        if($helpline->admin_id !== $this->getAdminId()){
            abort(403);
        }
        $helpline->delete();
        return redirect()->route('admin.helpline.index')->with('success', 'Helpline Deleted Success');
    }
}
