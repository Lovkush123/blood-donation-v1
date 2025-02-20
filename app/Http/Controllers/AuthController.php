<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\OTP;  // You may need a package for OTP like 'OTP' or 'Twilio'
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class AuthController extends Controller
{
 
    // Login using username and password
    public function login(Request $request)
    {
        // Validate request input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Find user by email
        $user = User::where('email', $request->email)->first();
    
        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        // Check if the user is active (if applicable)
        if ($user->status !== 'active') {
            return response()->json(['message' => 'Account is inactive. Contact support.'], 403);
        }
    
        // Generate API Token
        $token = $user->createToken('AppToken')->plainTextToken;
    
        // Return user details with token
        return response()->json([
            'message' => 'Login successful!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'address' => $user->address,
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'date_of_birth' => $user->date_of_birth,
                'age' => $user->age,
                'blood_type' => $user->blood_type,
                'last_donation_date' => $user->last_donation_date,
                'eligibility_status' => $user->eligibility_status,
                'credit_points' => $user->credit_points,
                'user_type' => $user->user_type,
                'status' => $user->status,
                'count' => $user->count,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ], 200);
    }
    // Register a new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'age' => 'nullable|integer',
            'blood_type' => 'nullable|string',
            'last_donation_date' => 'nullable|date',
            'eligibility_status' => 'nullable|string',
            'credit_points' => 'nullable|integer',
            'user_type' => 'nullable|string',
            'status' => 'nullable|string',
            'count' => 'nullable|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        try {
            $user = User::create([
                'full_name' => $request->full_name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'date_of_birth' => $request->date_of_birth,
                'age' => $request->age,
                'blood_type' => $request->blood_type,
                'last_donation_date' => $request->last_donation_date,
                'eligibility_status' => $request->eligibility_status,
                'credit_points' => $request->credit_points,
                'token' => Str::random(60),
                'user_type' => $request->user_type,
                'status' => $request->status,
                'count' => $request->count,
                'otp' => rand(100000, 999999),
            ]);
    
            return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Logout the user (API-based auth example)
    public function logout(Request $request)
    {
        try {
            Auth::user()->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json(['message' => 'Logged out successfully']);
        
        } catch (\Exception $e) {
            // Catch any exception and return a response with error message
            return response()->json([
                'error' => 'Logout failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

     // Fetch user by user_id
     public function fetchUser($userId)
     {
         try {
             // Find the user by user_id
             $user = User::findOrFail($userId);
 
             return response()->json([
                 'message' => 'User fetched successfully',
                 'user' => $user,
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'error' => 'User fetch failed',
                 'message' => $e->getMessage(),
             ], 500);
         }
     }
 

    public function update(Request $request, $userId)
{
    try {
        // Find the user by user_id
        $user = User::findOrFail($userId);

        // Validate the request to ensure all fields can be updated
        $request->validate([
            'full_name' => 'sometimes|required|string',
            'username' => 'sometimes|required|string|unique:users,username,' . $user->id,
            'email' => 'sometimes|required|string|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
            'phone_number' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
            'date_of_birth' => 'sometimes|required|date',
            'age' => 'sometimes|required|integer',
            'blood_type' => 'sometimes|required|string',
            'last_donation_date' => 'sometimes|required|date',
            'eligibility_status' => 'sometimes|required|string',
            'credit_points' => 'sometimes|required|integer',
            'token' => 'sometimes|required|string',
            'user_type' => 'sometimes|required|string|in:admin,user,hospital',
            'status' => 'sometimes|required|string',
            'count' => 'sometimes|required|integer',
        ]);

        // Update the user fields from the request if present
        if ($request->has('full_name')) {
            $user->full_name = $request->full_name;
        }
        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        if ($request->has('latitude')) {
            $user->latitude = $request->latitude;
        }
        if ($request->has('longitude')) {
            $user->longitude = $request->longitude;
        }
        if ($request->has('date_of_birth')) {
            $user->date_of_birth = $request->date_of_birth;
        }
        if ($request->has('age')) {
            $user->age = $request->age;
        }
        if ($request->has('blood_type')) {
            $user->blood_type = $request->blood_type;
        }
        if ($request->has('last_donation_date')) {
            $user->last_donation_date = $request->last_donation_date;
        }
        if ($request->has('eligibility_status')) {
            $user->eligibility_status = $request->eligibility_status;
        }
        if ($request->has('credit_points')) {
            $user->credit_points = $request->credit_points;
        }
        if ($request->has('token')) {
            $user->token = $request->token;
        }
        if ($request->has('user_type')) {
            $user->user_type = $request->user_type;
        }
        if ($request->has('status')) {
            $user->status = $request->status;
        }
        if ($request->has('count')) {
            $user->count = $request->count;
        }

        // Save the updated user data
        $user->save();

        // Return a success response
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Profile update failed',
            'message' => $e->getMessage(),
        ], 500);
    }
}


///fetch all user 
public function fetchAllUsers()
    {
      
        try {
        
            // Fetch all users with eligibility_status = 1
            $users = User::where('eligibility_status', 1)->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'message' => 'No eligible users found',
                ], 404);
            }

            return response()->json([
                'message' => 'Users fetched successfully',
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

// Forgot Password (using OTP)
public function forgotPassword(Request $request)
{
    // Validate email input
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
    ]);

    // If validation fails, return error response
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Check if user exists with provided email
    $user = User::where('email', $request->email)->first();
    
    if (!$user) {
        return response()->json(['message' => 'User not found!'], 404);
    }

    // Generate a random OTP
    $otp = rand(100000, 999999);
    $user->otp = $otp;
    $user->save();

    // Send OTP to email using SendOtpMail mailable
    Mail::to($user->email)->send(new SendOtpMail($otp));

    // Return success response
    return response()->json(['message' => 'OTP sent successfully to your email'], 200);
}

// Verify OTP for Password Reset
public function verifyOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'otp' => 'required|numeric',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();
    
    if (!$user || $user->otp != $request->otp) {
        return response()->json(['message' => 'Invalid OTP'], 400);
    }

    return response()->json(['message' => 'OTP verified successfully'], 200);
}

// Reset Password after OTP Verification
public function resetPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
        'otp' => 'required|numeric',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();
    
    if (!$user || $user->otp != $request->otp) {
        return response()->json(['message' => 'Invalid OTP'], 400);
    }

    $user->password = Hash::make($request->password);
    $user->otp = null;  // Clear OTP after successful reset
    $user->save();

    return response()->json(['message' => 'Password reset successfully'], 200);
}
public function fetchNearbyUsers(Request $request)
{
    $validator = Validator::make($request->all(), [
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'radius' => 'required|numeric', // Radius in kilometers
        'min_age' => 'nullable|integer',
        'max_age' => 'nullable|integer',
        'blood_type' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $latitude = $request->latitude;
    $longitude = $request->longitude;
    $radius = $request->radius;
    $minAge = $request->min_age;
    $maxAge = $request->max_age;
    $bloodType = $request->blood_type;

    $query = User::select('*')
        ->selectRaw('(
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ) AS distance', [$latitude, $longitude, $latitude])
        ->having('distance', '<=', $radius)
        ->orderBy('distance');

    if (!empty($minAge) && !empty($maxAge)) {
        $query->whereBetween('age', [$minAge, $maxAge]);
    } elseif (!empty($minAge)) {
        $query->where('age', '>=', $minAge);
    } elseif (!empty($maxAge)) {
        $query->where('age', '<=', $maxAge);
    }

    if (!empty($bloodType)) {
        $query->where('blood_type', $bloodType);
    }

    $nearbyUsers = $query->get();

    return response()->json(['nearby_users' => $nearbyUsers], 200);
}



}
