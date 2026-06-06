<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CommitteeMemberAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated as admin via the 'admin' guard
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }
        
        // Check if user is authenticated as a committee member via session
        if (Session::has('committee_member')) {
            $committeeMember = Session::get('committee_member');
            
            // Verify the committee member still exists in the database
            $dbCommitteeMember = \App\Models\CommitteePerson::find($committeeMember->id);
            if ($dbCommitteeMember) {
                // Check if the committee member has one of the allowed roles
                $allowedRoles = ['president', 'vice president', 'treasurer', 'secretary', 'joint secretary', 'joint treasurer'];
                $role = strtolower($dbCommitteeMember->post_name);
                
                if (in_array($role, $allowedRoles)) {
                    // Update the session with fresh data
                    Session::put('committee_member', $dbCommitteeMember);
                    return $next($request);
                }
            }
            
            // If committee member doesn't exist or doesn't have proper role, logout
            Session::forget('committee_member');
        }
        
        // Redirect to admin login if not authenticated as either admin or authorized committee member
        return redirect()->route('admin.login');
    }
}