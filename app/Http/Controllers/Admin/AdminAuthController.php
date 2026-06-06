<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\CommitteePerson;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Show the regular Admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle the Admin login request using the dedicated 'admin' guard.
     * Supports both email/password and phone/password authentication for committee members.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ]);

        $login = $request->login;
        $password = $request->password;

        // Check if login is an email
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Email login - use admin guard
            $credentials = [
                'email' => $login,
                'password' => $password
            ];

            if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
        } else {
            // Phone number login - check if it's a committee member
            $committeeMember = CommitteePerson::where('phone', $login)->first();
            
            if ($committeeMember && Hash::check($password, $committeeMember->password ?? '')) {
                // Check if the committee member has one of the allowed roles
                $allowedRoles = ['president', 'vice president', 'treasurer', 'secretary', 'joint secretary', 'joint treasurer'];
                $role = strtolower($committeeMember->post_name);
                
                if (in_array($role, $allowedRoles)) {
                    // Since committee members are not stored in the admins table, 
                    // we'll create a session that identifies them as authorized committee members.
                    $request->session()->put('committee_member', $committeeMember);
                    $request->session()->regenerate();
                    return redirect()->intended(route('admin.dashboard'));
                }
            }
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }
    
    /**
     * Log the Admin user out of the application.
     */
    public function logout(Request $request)
    {
        // Logout admin if authenticated as admin
        Auth::guard('admin')->logout();
        
        // Clear committee member session if exists
        $request->session()->forget('committee_member');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}