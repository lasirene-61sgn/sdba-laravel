<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportCategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:support_categories,name,NULL,id,admin_id,' . Auth::guard('admin')->id()]);

        $supportCategory = SupportCategory::create([
            'name' => $request->name,
            'admin_id' => Auth::guard('admin')->id(), // Assign the current admin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Support Category created and added to dropdown.',
            'support_category' => $supportCategory,
        ], 201);
    }
}