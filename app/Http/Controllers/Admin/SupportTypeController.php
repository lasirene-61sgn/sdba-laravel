<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:support_types,name,NULL,id,admin_id,' . Auth::guard('admin')->id()]);

        $supportType = SupportType::create([
            'name' => $request->name,
            'admin_id' => Auth::guard('admin')->id(), // Assign the current admin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Support Type created and added to dropdown.',
            'support_type' => $supportType,
        ], 201);
    }
}