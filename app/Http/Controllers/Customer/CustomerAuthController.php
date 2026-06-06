<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\WhatsAppOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('customer.login');
    }

    /**
     * Handle mobile number submission for OTP generation
     */
    public function sendOTP(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|string|exists:customers,mobile',
            ]);

            // Find customer by mobile number
            $customer = Customer::where('mobile', $request->mobile)->first();
            
            // Initialize WhatsApp OTP service
            $whatsappOTPService = new WhatsAppOTPService();
            
            // Generate a random 6-digit OTP
            $otp = $whatsappOTPService->generateOTP();
            $customer->otp = $otp;
            $customer->otp_expires_at = now()->addMinutes(10); // OTP expires in 10 minutes
            $customer->save();

            // Send OTP via WhatsApp using MSG91
            $result = $whatsappOTPService->sendOTP($request->mobile, $otp);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully via WhatsApp',
                    'data' => [
                        'mobile' => $request->mobile,
                        'message' => 'Please check your WhatsApp for the OTP'
                    ]
                ]);
            } else {
                // If WhatsApp sending fails, return error
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'data' => null
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not found in our records. Please check the number and try again.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the OTP verification form
     */
    public function showOTPForm(Request $request)
    {
        $mobile = $request->query('mobile');
        return view('customer.verify_otp', compact('mobile'));
    }

    /**
     * Verify OTP and show password setup form if needed
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|exists:customers,mobile',
            'otp' => 'required|string',
        ]);

        // Find customer by mobile number
        $customer = Customer::where('mobile', $request->mobile)->first();

        // Verify OTP
        if ($customer->otp !== $request->otp || !$customer->otp_expires_at || $customer->otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        // If password is already set, log in the customer
        if ($customer->is_password_set) {
            Auth::guard('customer')->login($customer);
            return redirect()->route('customer.dashboard');
        }

        // If password is not set, show password setup form
        return view('customer.set_password', compact('customer'));
    }

    /**
     * Set password for the customer
     */
    public function setPassword(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        
        // Set password
        $customer->password = Hash::make($request->password);
        $customer->is_password_set = true;
        $customer->otp = null;
        $customer->otp_expires_at = null;
        $customer->save();

        // Log in the customer
        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard')->with('success', 'Password set successfully!');
    }

    /**
     * Show the login form with password
     */
    public function showPasswordLoginForm()
    {
        return view('customer.password_login');
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

        $customer = Customer::where('mobile', $request->mobile)->first();

        if (!$customer || !$customer->is_password_set || !Hash::check($request->password, $customer->password)) {
            return back()->withErrors(['password' => 'Invalid mobile number or password']);
        }

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('customer.forgot_password');
    }

    /**
     * Handle forgot password request
     */
    public function sendForgotPasswordOTP(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|string|exists:customers,mobile',
            ]);

            // Find customer by mobile number
            $customer = Customer::where('mobile', $request->mobile)->first();
            
            // Initialize WhatsApp OTP service
            $whatsappOTPService = new WhatsAppOTPService();
            
            // Generate a random 6-digit OTP
            $otp = $whatsappOTPService->generateOTP();
            $customer->otp = $otp;
            $customer->otp_expires_at = now()->addMinutes(10); // OTP expires in 10 minutes
            $customer->save();

            // Send OTP via WhatsApp using MSG91
            $result = $whatsappOTPService->sendOTP($request->mobile, $otp);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully via WhatsApp',
                    'data' => [
                        'mobile' => $request->mobile,
                        'message' => 'Please check your WhatsApp for the OTP'
                    ]
                ]);
            } else {
                // If WhatsApp sending fails, return error
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'data' => null
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not found in our records. Please check the number and try again.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm(Request $request)
    {
        $mobile = $request->query('mobile');
        return view('customer.reset_password', compact('mobile'));
    }

    /**
     * Reset password
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

        // Verify OTP
        if ($customer->otp !== $request->otp || !$customer->otp_expires_at || $customer->otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        // Set new password
        $customer->password = Hash::make($request->password);
        $customer->is_password_set = true;
        $customer->otp = null;
        $customer->otp_expires_at = null;
        $customer->save();

        // Log in the customer
        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard')->with('success', 'Password reset successfully!');
    }

    /**
     * Logout the customer
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        return redirect()->route('customer.login');
    }
}