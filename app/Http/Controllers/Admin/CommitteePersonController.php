<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitteePerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CommitteePersonController extends Controller
{
    public function index()
    {
        // Check if admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please log in to access the committee members.');
        }
        
        $adminId = Auth::guard('admin')->id();
        $people = CommitteePerson::where('admin_id', $adminId)->orderBy('sort_order', 'asc')->paginate(10);
        return view('admin.committee_person.index', compact('people'));
    }

    public function create()
    {
        // Check if admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please log in to create a committee member.');
        }
        
        // No posts needed here!
        return view('admin.committee_person.create');
    }

    public function store(Request $request)
    {
        // Check if admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please log in to create a committee member.');
        }
        
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:committee_people,phone',
            'password' => 'required|string|min:6|confirmed',
            'post_name' => 'nullable|string|max:100', // VALIDATION FOR NEW FIELD
            'sort_order' => 'nullable|integer|min:0', // VALIDATION FOR SORT ORDER
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'state' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('committee_images', 'public');
        }

        CommitteePerson::create([
            'admin_id' => Auth::guard('admin')->id(), // CAPTURING ADMIN ID
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password), // HASHING PASSWORD
            'post_name' => $request->post_name,     // STORING POST NAME
            'sort_order' => $request->sort_order ?? 0, // STORING SORT ORDER
            'image_path' => $imagePath,
            'status' => $request->status,
            'state' => $request->state,
        ]);

        return redirect()->route('admin.committee.index')->with('success', 'Committee member created successfully!');
    }

    public function edit($id) 
    {
        // Directly find the committee member by ID without authorization checks
        $committee = CommitteePerson::find($id);
        
        // Check if committee member exists
        if (!$committee) {
            return redirect()->route('admin.committee.index')->with('error', 'Committee member not found.');
        }
        
        return view('admin.committee_person.edit', compact('committee'));
    }

    public function update(Request $request, $id)
    {
        // Directly find the committee member by ID without authorization checks
        $committee = CommitteePerson::find($id);
        
        // Check if committee member exists
        if (!$committee) {
            return redirect()->route('admin.committee.index')->with('error', 'Committee member not found.');
        }
        
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:committee_people,phone,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'post_name' => 'nullable|string|max:100', // VALIDATION FOR POST NAME
            'sort_order' => 'nullable|integer|min:0', // VALIDATION FOR SORT ORDER
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'state' => 'nullable|string',
        ]);

        $imagePath = $committee->image_path;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('committee_images', 'public');
        }

        // Prepare update data
        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'post_name' => $request->post_name,
            'sort_order' => $request->sort_order ?? 0,
            'image_path' => $imagePath,
            'status' => $request->status,
            'state' => $request->state,
        ];
        
        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $committee->update($updateData);

        return redirect()->route('admin.committee.index')->with('success', 'Committee member updated successfully!');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Directly find the committee member by ID without authorization checks
        $committee = CommitteePerson::find($id);
        
        // Check if committee member exists
        if (!$committee) {
            return redirect()->route('admin.committee.index')->with('error', 'Committee member not found.');
        }
        
        // Delete the image if it exists
        if ($committee->image_path) {
            Storage::disk('public')->delete($committee->image_path);
        }
        
        // Delete the committee member
        $committee->delete();
        
        return redirect()->route('admin.committee.index')->with('success', 'Committee member deleted successfully!');
    }

    public function showBulkUploadForm()
    {
        if(!Auth::guard('admin')->check())
        {
            return redirect()->route('admin.login')->with('error', 'Please log in to access the bulk uplaad feature');
        }
        return view('admin.committee_person.bulk_upload');
    }

    public function bulkUpload(Request $request)
    {
        if(!Auth::guard('admin')->check()){
            return redirect()->route('admin.login')->with('Error', 'Please Log in to a access the feature');
        }
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        if(($handle = fopen($filePath, 'r'))  !== false){
            $header =fgetcsv($handle, 1000, ',');

            $rowCount = 0;
            $errors = [];
            $adminId = Auth::guard('admin')->id();

            while(($data = fgetcsv($handle, 1000, ',')) !== false){
                $rowCount++;

                if(count($header) !== count($data)){
                    $errors[] = "Row {$rowCount}: Column count mismatch.";
                    continue;
                }

                $row = array_combine($header, $data);
                $row = array_map('trim', $row);

                $validator = Validator::make($row, [
                    'name'       => 'required|string|max:100',
                    'phone'      => 'required|string|max:20|unique:committee_people,phone',
                    'password'   => 'required|string|min:6',
                    'post_name'  => 'nullable|string|max:100',
                    'sort_order' => 'nullable|integer|min:0',
                    'status'     => 'required|in:active,inactive',
                    'state'      => 'nullable|string',
                ]);

                if($validator->fails()){
                    $errors[] = "Row {$rowCount} (" . ($row['name'] ?? 'Unknown') . "): " . implode(', ', $validator->errors()->all());
                    continue;
                }
                CommitteePerson::create([
                    'admin_id'   => $adminId,
                    'name'       => $row['name'],
                    'phone'      => $row['phone'],
                    'password'   => Hash::make($row['password']), // Hash it securely
                    'post_name'  => $row['post_name'] ?? null,
                    'sort_order' => isset($row['sort_order']) && $row['sort_order'] !== '' ? (int)$row['sort_order'] : 0,
                    'image_path' => null, // Note: Images cannot be easily uploaded via standard CSV text.
                    'status'     => strtolower($row['status']),
                    'state'      => $row['state'] ?? null,
                ]);
            }
            fclose($handle);

            if(count($errors) > 0){
                return redirect()->route('admin.committee.index')
                    ->with('success', 'Bulk upload completed with some skipped rows.')
                    ->with('error_list', $errors);
            }
            return redirect()->route('admin.committee.index')->with('success', 'All committee members uploaded successfully!');
        }
        return redirect()->back()->with('error', 'Could not open the uploaded file.');
    }
}