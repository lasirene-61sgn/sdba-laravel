# Customer Login API Changes

## Overview
Updated the customer login API to support a unified login flow that handles both OTP-based login for new users and password-based login for existing users.

## Changes Made

### 1. Updated AuthController (API)
**File:** `app/Http/Controllers/Api/Customer/AuthController.php`

- Added a new `login()` method that provides unified login functionality
- The method checks if the customer has set a password:
  - If password is set: allows login with either password or OTP
  - If password is not set: requires OTP login first
- Updated `sendOTP()` method to check if customer already has password set:
  - If password is set: returns error suggesting to use password login instead
  - If no password set: sends OTP as normal
- Maintains backward compatibility with existing `loginWithPassword()` method
- Preserves all existing functionality for password reset, OTP verification, etc.

### 2. Updated API Routes
**File:** `routes/api.php`

- Changed the customer login route from `loginWithPassword` to the new unified `login` method
- Route: `POST /api/customer/login`

## New Login Flow

### For First-Time Users (No Password Set):
1. User requests OTP: `POST /api/customer/send-otp`
2. User verifies with OTP: `POST /api/customer/login` with `mobile` and `otp`
3. User can optionally set a permanent password: `POST /api/customer/set-password`

### For Existing Users (Password Set):
1. User can login with password: `POST /api/customer/login` with `mobile` and `password`
2. OR user can login with OTP: `POST /api/customer/login` with `mobile` and `otp`
3. User can reset password if forgotten: `POST /api/customer/forgot-password-otp` and `POST /api/customer/reset-password`

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/customer/send-otp` | Send OTP to customer's mobile via WhatsApp |
| POST | `/api/customer/login` | Unified login (accepts mobile + password OR mobile + otp) |
| POST | `/api/customer/set-password` | Set a permanent password after first-time OTP login |
| POST | `/api/customer/forgot-password-otp` | Send OTP for password reset |
| POST | `/api/customer/reset-password` | Reset password using OTP |

## Key Features

1. **Unified Login**: Single endpoint handles both OTP and password authentication
2. **Smart Authentication**: Automatically determines if user should use OTP or password
3. **Backward Compatibility**: Existing methods still work as before
4. **Security**: Maintains all existing security measures (OTP expiration, password hashing)
5. **Flexibility**: Users can choose to login with either method if they have a password set

## Usage Examples

### Login with Password (for users who have set a password):
```json
{
  "mobile": "9876543210",
  "password": "your_password"
}
```

### Login with OTP (for users who have set a password):
```json
{
  "mobile": "9876543210",
  "otp": "123456"
}
```

### First-time login with OTP:
```json
{
  "mobile": "9876543210",
  "otp": "123456"
}
```

### Response Format:
```json
{
  "status": "success",
  "message": "Login successful",
  "token": "generated_token_here",
  "customer": { ...customer_data... }
}
```