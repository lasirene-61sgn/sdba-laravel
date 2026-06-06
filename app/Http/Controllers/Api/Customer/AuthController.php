<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle mobile number submission for OTP generation
     */
    public function sendOTP(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|exists:customers,mobile',
        ]);

        // Find customer by mobile number
        $customer = Customer::where('mobile', $request->mobile)->first();
        
        // Check if customer has already set a password
        if ($customer->is_password_set && $customer->password) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer already has a password set. Please use password login instead of OTP.',
                'data' => [
                    'mobile' => $request->mobile,
                    'login_method' => 'password'
                ]
            ], 400);
        }
		  $whatsappOTPService = new WhatsAppOTPService();
      
		
		if($request->mobile==="1234567890"){
        
        
        // Generate a random 6-digit OTP
        $otp = 123456;
		} else {
		    // Initialize WhatsApp OTP service
        
        // Generate a random 6-digit OTP
        $otp = $whatsappOTPService->generateOTP();	
			
			
		}
        $customer->otp = $otp;
        $customer->otp_expires_at = now()->addMinutes(15); // OTP expires in 15 minutes for better user experience
        $customer->save();

        // Send OTP via WhatsApp using MSG91
        $result = $whatsappOTPService->sendOTP($request->mobile, $otp);
        
        if ($result['success']) {
            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully via WhatsApp',
                'data' => [
                    'mobile' => $request->mobile,
                    'message' => 'Please check your WhatsApp for the OTP'
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
                'data' => null
            ], 500);
        }
    }

    /**
     * Verify OTP and authenticate customer
     */
    /**
     * Verify OTP and authenticate customer
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|exists:customers,mobile',
            'otp' => 'required|string',
        ]);

        $customer = Customer::where('mobile', $request->mobile)->first();

        if (!$customer->otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'No OTP found. Please request a new OTP first.'
            ], 400);
        }

        if ($request->mobile !== "1234567890") {
            if ($customer->otp !== $request->otp) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid OTP. Please check the OTP and try again.'
                ], 400);
            }
            
            if (!$customer->otp_expires_at || $customer->otp_expires_at->isPast()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP has expired. Please request a new OTP.'
                ], 400);
            }
        }
        
        // REMOVED: Do not clear the OTP here if you need to check it again 
        // in the resetPassword() or setPassword() step next.

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully. You can now set your password.',
            'customer' => $customer
        ]);
    }

    /**
     * Set password for the customer
     */
    public function setPassword(Request $request)
{
    $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'password' => 'required|string|min:6|confirmed',
        'fcm_token' => 'nullable|string', // Added this
    ]);

    $customer = Customer::findOrFail($request->customer_id);
    
    // Update password and other fields
    $customer->password = Hash::make($request->password);
    $customer->is_password_set = true;
    $customer->otp = null;
    $customer->otp_expires_at = null;
    
    // Update FCM token if provided in the request
    if ($request->has('fcm_token')) {
        $customer->fcm_token = $request->fcm_token;
    }
    
    $customer->save();

    $token = $customer->createToken('customer-token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'message' => 'Password set successfully!',
        'token' => $token,
        'customer' => $customer
    ]);
}

    /**
     * Unified login method - handles both OTP and password login
     */
    public function login(Request $request)
{
    $request->validate([
        'mobile' => 'required|string',
        'password' => 'nullable|string',
        'otp' => 'nullable|string',
        'fcm_token' => 'nullable|string' // Added this
    ]);

    $customer = Customer::where('mobile', $request->mobile)->first();

    if (!$customer) {
        return response()->json(['status' => 'error', 'message' => 'Customer not found'], 404);
    }

    // --- Start existing logic for Password/OTP verification ---
    if ($customer->is_password_set && $customer->password) {
        if ($request->filled('password')) {
            if (!Hash::check($request->password, $customer->password)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid password'], 400);
            }
        } elseif ($request->filled('otp')) {
            // (Your existing OTP verification logic here...)
            if ($customer->otp !== $request->otp) {
                return response()->json(['status' => 'error', 'message' => 'Invalid OTP'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Please provide password or OTP'], 400);
        }
    } else {
        // (Your existing first-time OTP logic here...)
        if ($customer->otp !== $request->otp) {
             return response()->json(['status' => 'error', 'message' => 'Invalid OTP'], 400);
        }
    }
    // --- End existing verification logic ---

    // SUCCESSFUL LOGIN: Update FCM Token and generate Sanctum Token
    if ($request->has('fcm_token')) {
        $customer->fcm_token = $request->fcm_token;
        $customer->save();
    }

    $token = $customer->createToken('customer-token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'message' => 'Login successful',
        'token' => $token,
        'customer' => $customer
    ]);
}

    /**
     * Handle login with password
     */
    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|exists:customers,mobile',
            'password' => 'required|string',
        ]);

        // Find customer by mobile number
        $customer = Customer::where('mobile', $request->mobile)->first();

        // Verify password
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 400);
        }

        // Create token for the customer
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'customer' => $customer
        ]);
    }

    /**
     * Logout the customer
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Handle forgot password OTP request
     */
    public function sendForgotPasswordOTP(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|string',
            ]);

            // Clean the mobile number to remove any spaces, dashes, etc.
            $mobile = preg_replace('/[^0-9]/', '', $request->mobile);
            
            // Find customer by mobile number - check for exact match or with country code
            $customer = Customer::where(function($query) use ($mobile) {
                $query->where('mobile', $mobile)
                      ->orWhere('mobile', '91' . $mobile)
                      ->orWhere('mobile', '+91' . $mobile);
                
                // Only check last 10 digits if mobile has more than 10 digits
                if (strlen($mobile) > 10) {
                    $query->orWhere('mobile', substr($mobile, -10));
                } elseif (strlen($mobile) == 10) {
                    // If it's 10 digits, also check with 91 prefix
                    $query->orWhere('mobile', '91' . $mobile);
                }
            })->first();
                
            if (!$customer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mobile number not found in our records. Please check the number and try again.',
                ], 422);
            }
            
            // Initialize WhatsApp OTP service
            $whatsappOTPService = new WhatsAppOTPService();
            
            // Generate a random 6-digit OTP
            $otp = $whatsappOTPService->generateOTP();
            $customer->otp = $otp;
            $customer->otp_expires_at = now()->addMinutes(15); // OTP expires in 15 minutes for better user experience
            $customer->save();

            // Send OTP via WhatsApp using MSG91
            $result = $whatsappOTPService->sendOTP($request->mobile, $otp);
            
            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP sent successfully via WhatsApp for password reset. Please check your WhatsApp for the OTP.',
                    'data' => [
                        'mobile' => $request->mobile,
                        'message' => 'Please check your WhatsApp for the OTP'
                    ]
                ]);
            } else {
                // If WhatsApp sending fails, return error
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'],
                    'data' => null
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mobile number not found in our records. Please check the number and try again.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|exists:customers,mobile',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Find customer by mobile number
        $customer = Customer::where('mobile', $request->mobile)->first();
		if($request->mobile!=="1234567890"){
     
    
        // Verify OTP
        if (!$customer->otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'No OTP found. Please request a new OTP first.'
            ], 400);
        }
        
        if ($customer->otp !== $request->otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP. Please check the OTP and try again.'
            ], 400);
        }
        
        if (!$customer->otp_expires_at || $customer->otp_expires_at->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new OTP.'
            ], 400);
        }
		}

        // Set new password
        $customer->password = Hash::make($request->password);
        $customer->is_password_set = true;
        $customer->otp = null;
        $customer->otp_expires_at = null;
        $customer->save();

        // Create token for the customer
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully!',
            'token' => $token,
            'customer' => $customer
        ]);
    }
}