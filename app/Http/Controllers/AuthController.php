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
use Carbon\Carbon;

class AuthController extends Controller
{
 
    // Login using username and password
public function login(Request $request)
{
    // Validate request input
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|max:255',
        'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Find user by username
    $user = User::where('username', $request->username)->first();

    // Check if user exists and password is correct
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Generate API Token
    $token = $user->createToken('AppToken')->plainTextToken;

    // Return user details with token
    return response()->json([
        'message' => 'Login successful!',
        'token' => $token,
        'user' => [
            'user_id' => $user->user_id,
            'full_name' => $user->full_name,
            'username' => $user->username,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'current_latitude' => $user->current_latitude, // Added current latitude
            'current_longitude' => $user->current_longitude, // Added current longitude
            'date_of_birth' => $user->date_of_birth,
            'age' => $user->age,
            'blood_type' => $user->blood_type,
            'last_donation_date' => $user->last_donation_date,
            'eligibility_status' => $user->eligibility_status,
            'credit_points' => $user->credit_points,
            'user_type' => $user->user_type,
            'status' => $user->status,
            'count' => $user->count,
            'donor_type' => $user->donor_type, // Added donor type
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]
    ], 200);
}

    // Register a new user
// public function register(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'full_name' => 'required|string|max:255',
//         'username' => 'required|string|max:255|unique:users',
//         'email' => 'required|string|email|max:255|unique:users',
//         'password' => 'required|string|min:8',
//         'phone_number' => 'required|string',
//         'address' => 'required|string',
//         'date_of_birth' => 'required|date',
//         'latitude' => 'nullable|numeric',
//         'longitude' => 'nullable|numeric',
//         'current_latitude' => 'nullable|numeric', // Added current latitude validation
//         'current_longitude' => 'nullable|numeric', // Added current longitude validation
//         'blood_type' => 'nullable|string',
//         'last_donation_date' => 'nullable|date',
//         'eligibility_status' => 'nullable|string',
//         'credit_points' => 'nullable|integer',
//         'user_type' => 'nullable|string',
//         'count' => 'nullable|integer',
//         'donor_type' => 'nullable|string', // Added donor type validation
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['errors' => $validator->errors()], 422);
//     }

//     try {
//         $user = User::create([
//             'full_name' => $request->full_name,
//             'username' => $request->username,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'phone_number' => $request->phone_number,
//             'address' => $request->address,
//             'latitude' => $request->latitude,
//             'longitude' => $request->longitude,
//             'current_latitude' => $request->current_latitude, // Added current latitude
//             'current_longitude' => $request->current_longitude, // Added current longitude
//             'date_of_birth' => $request->date_of_birth,
//             'blood_type' => $request->blood_type,
//             'last_donation_date' => $request->last_donation_date,
//             'eligibility_status' => '1',
//             'credit_points' =>0,
//             'token' => Str::random(60),
//             'user_type' => $request->user_type,
//             'status' => 'pending', // Set status to pending
//             'otp' => rand(100000, 999999),
//             'count' =>0,
//             'donor_type' => $request->donor_type, // Added donor type field
//         ]);

//         return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }

// public function register(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'username' => 'required|string|max:255|unique:users',
//         'email' => 'required|string|email|max:255|unique:users',
//         'phone_number' => 'required|string',
//         'password' => 'required|string|min:8',

//         // Optional fields
//         'full_name' => 'nullable|string|max:255',
//         'address' => 'nullable|string',
//         'date_of_birth' => 'nullable|date',
//         'latitude' => 'nullable|numeric',
//         'longitude' => 'nullable|numeric',
//         'current_latitude' => 'nullable|numeric',
//         'current_longitude' => 'nullable|numeric',
//         'blood_type' => 'nullable|string',
//         'last_donation_date' => 'nullable|date',
//         'eligibility_status' => 'nullable|string',
//         'credit_points' => 'nullable|integer',
//         'user_type' => 'nullable|string',
//         'count' => 'nullable|integer',
//         'donor_type' => 'nullable|string',
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['errors' => $validator->errors()], 422);
//     }

//     try {
//         $user = User::create([
//              'user_id' => $request->user_id,
//             'full_name' => $request->full_name,
//             'username' => $request->username,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'phone_number' => $request->phone_number,
//             'address' => $request->address,
//             'latitude' => $request->latitude,
//             'longitude' => $request->longitude,
//             'current_latitude' => $request->current_latitude,
//             'current_longitude' => $request->current_longitude,
//             'date_of_birth' => $request->date_of_birth,
//             'blood_type' => $request->blood_type,
//             'last_donation_date' => $request->last_donation_date,
//             'eligibility_status' => '1',
//             'credit_points' => 0,
//             'token' => Str::random(60),
//             'user_type' => $request->user_type,
//             'status' => 'pending',
//             'otp' => rand(100000, 999999),
//             'count' => 0,
//             'donor_type' => $request->donor_type,
//         ]);

//         return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }
public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'phone_number' => 'required|string',
        'password' => 'required|string|min:8',

        // Optional fields
        'full_name' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'date_of_birth' => 'nullable|date',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'current_latitude' => 'nullable|numeric',
        'current_longitude' => 'nullable|numeric',
        'blood_type' => 'nullable|string',
        'last_donation_date' => 'nullable|date',
        'eligibility_status' => 'nullable|string',
        'credit_points' => 'nullable|integer',
        'user_type' => 'nullable|string',
        'count' => 'nullable|integer',
        'donor_type' => 'nullable|string',
        'gender' => 'nullable|string|max:10', // New field
        'sub_user_type' => 'nullable|string|max:50', // New field
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        $user = User::create([
            'user_id' => $request->user_id,
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'current_latitude' => $request->current_latitude,
            'current_longitude' => $request->current_longitude,
            'date_of_birth' => $request->date_of_birth,
            'blood_type' => $request->blood_type,
            'last_donation_date' => $request->last_donation_date,
            'eligibility_status' => '1',
            'credit_points' => 0,
            'token' => Str::random(60),
            'user_type' => $request->user_type,
            'status' => 'pending',
            'otp' => rand(100000, 999999),
            'count' => 0,
            'donor_type' => $request->donor_type,
            'gender' => $request->gender, // New field
            'sub_user_type' => $request->sub_user_type, // New field
        ]);

        return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function registerer(Request $request)
{
    try {
        // Validate and calculate age from date_of_birth before validation
        if (!$request->filled('date_of_birth') || !strtotime($request->date_of_birth)) {
            return response()->json(['error' => 'Invalid or missing date_of_birth'], 422);
        }
        
        $dob = new DateTime($request->date_of_birth);
        $today = new DateTime();
        $age = $today->diff($dob)->y; // Get age in years
        echo $age;
        // Validation
        $validator = Validator::make($request->all() + ['age' => $age], [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'blood_type' => 'nullable|string',
            'last_donation_date' => 'nullable|date',
            'eligibility_status' => 'nullable|string',
            'credit_points' => 'nullable|integer',
            'user_type' => 'nullable|string',
            'count' => 'nullable|integer',
            'age' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create new user
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
            'age' => $age, // Store calculated age
            'blood_type' => $request->blood_type,
            'last_donation_date' => $request->last_donation_date,
            'eligibility_status' => '1',
            'credit_points' => 0,
            'token' => Str::random(60),
            'user_type' => $request->user_type,
            'status' => 'pending', // Set status to pending
            'otp' => rand(100000, 999999),
            'count' => 0, // Ensure count has a default value
        ]);

        return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
    } catch (\Exception $e) {
        \Log::error('User Registration Error: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong! Please try again.'], 500);
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
 

//     public function update(Request $request, $userId)
// {
//     try {
//         // Find the user by user_id
//         $user = User::findOrFail($userId);

//         // Validate the request to ensure all fields can be updated
//         $request->validate([
//             'full_name' => 'sometimes|required|string',
//             'username' => 'sometimes|required|string|unique:users,username,' . $user->id,
//             'email' => 'sometimes|required|string|email|unique:users,email,' . $user->id,
//             'password' => 'sometimes|required|string|min:8',
//             'phone_number' => 'sometimes|required|string',
//             'address' => 'sometimes|required|string',
//             'latitude' => 'sometimes|required|numeric',
//             'longitude' => 'sometimes|required|numeric',
//             'date_of_birth' => 'sometimes|required|date',
//             'age' => 'sometimes|required|integer',
//             'blood_type' => 'sometimes|required|string',
//             'last_donation_date' => 'sometimes|required|date',
//             'eligibility_status' => 'sometimes|required|string',
//             'credit_points' => 'sometimes|required|integer',
//             'token' => 'sometimes|required|string',
//             'user_type' => 'sometimes|required|string|in:admin,user,hospital',
//             'status' => 'sometimes|required|string',
//             'count' => 'sometimes|required|integer',
//         ]);

//         // Update the user fields from the request if present
//         if ($request->has('full_name')) {
//             $user->full_name = $request->full_name;
//         }
//         if ($request->has('username')) {
//             $user->username = $request->username;
//         }
//         if ($request->has('email')) {
//             $user->email = $request->email;
//         }
//         if ($request->has('password')) {
//             $user->password = Hash::make($request->password);
//         }
//         if ($request->has('phone_number')) {
//             $user->phone_number = $request->phone_number;
//         }
//         if ($request->has('address')) {
//             $user->address = $request->address;
//         }
//         if ($request->has('latitude')) {
//             $user->latitude = $request->latitude;
//         }
//         if ($request->has('longitude')) {
//             $user->longitude = $request->longitude;
//         }
//         if ($request->has('date_of_birth')) {
//             $user->date_of_birth = $request->date_of_birth;
//         }
//         if ($request->has('age')) {
//             $user->age = $request->age;
//         }
//         if ($request->has('blood_type')) {
//             $user->blood_type = $request->blood_type;
//         }
//         if ($request->has('last_donation_date')) {
//             $user->last_donation_date = $request->last_donation_date;
//         }
//         if ($request->has('eligibility_status')) {
//             $user->eligibility_status = $request->eligibility_status;
//         }
//         if ($request->has('credit_points')) {
//             $user->credit_points = $request->credit_points;
//         }
//         if ($request->has('token')) {
//             $user->token = $request->token;
//         }
//         if ($request->has('user_type')) {
//             $user->user_type = $request->user_type;
//         }
//         if ($request->has('status')) {
//             $user->status = $request->status;
//         }
//         if ($request->has('count')) {
//             $user->count = $request->count;
//         }

//         // Save the updated user data
//         $user->save();

//         // Return a success response
//         return response()->json([
//             'message' => 'Profile updated successfully',
//             'user' => $user,
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => 'Profile update failed',
//             'message' => $e->getMessage(),
//         ], 500);
//     }
// }
public function update(Request $request, $userId)
{
    try {
        // Find the user by ID
        $user = User::findOrFail($userId);

        // Validate the request
        $request->validate([
            'full_name' => 'sometimes|nullable|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'sometimes|nullable|string',
            'password' => 'sometimes|nullable|string|min:8',
            'address' => 'sometimes|nullable|string',
            'latitude' => 'sometimes|nullable|numeric',
            'longitude' => 'sometimes|nullable|numeric',
            'current_latitude' => 'sometimes|nullable|numeric',
            'current_longitude' => 'sometimes|nullable|numeric',
            'date_of_birth' => 'sometimes|nullable|date',
            'blood_type' => 'sometimes|nullable|string',
            'last_donation_date' => 'sometimes|nullable|date',
            'eligibility_status' => 'sometimes|nullable|string',
            'credit_points' => 'sometimes|nullable|integer',
            'token' => 'sometimes|nullable|string',
            'user_type' => 'sometimes|nullable|string|in:admin,user,hospital',
            'status' => 'sometimes|nullable|string',
            'count' => 'sometimes|nullable|integer',
            'donor_type' => 'sometimes|nullable|string',
            'gender' => 'sometimes|nullable|string|max:10',
            'sub_user_type' => 'sometimes|nullable|string|max:50',
        ]);

        // Fill user model with request data
        $user->fill($request->only([
            'full_name', 'username', 'email', 'phone_number', 'address', 'latitude', 'longitude',
            'current_latitude', 'current_longitude', 'date_of_birth', 'blood_type', 'last_donation_date',
            'eligibility_status', 'credit_points', 'token', 'user_type', 'status', 'count', 'donor_type',
            'gender', 'sub_user_type'
        ]));

        // Update password if provided
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        // Save changes
        $user->save();

        // Return full user details
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'address' => $user->address,
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'current_latitude' => $user->current_latitude,
                'current_longitude' => $user->current_longitude,
                'date_of_birth' => $user->date_of_birth,
                'blood_type' => $user->blood_type,
                'last_donation_date' => $user->last_donation_date,
                'eligibility_status' => $user->eligibility_status,
                'credit_points' => $user->credit_points,
                'token' => $user->token,
                'user_type' => $user->user_type,
                'status' => $user->status,
                'count' => $user->count,
                'donor_type' => $user->donor_type,
                'gender' => $user->gender,
                'sub_user_type' => $user->sub_user_type,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Profile update failed',
            'message' => $e->getMessage(),
        ], 500);
    }
}




///fetch all user 
// public function fetchAllUsers()
//     {
      
//         try {
        
//             // Fetch all users with eligibility_status = 1
//             $users = User::where('eligibility_status', 1)->get();

//             if ($users->isEmpty()) {
//                 return response()->json([
//                     'message' => 'No eligible users found',
//                 ], 404);
//             }

//             return response()->json([
//                 'message' => 'Users fetched successfully',
//                 'users' => $users,
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'error' => 'Failed to fetch users',
//                 'message' => $e->getMessage(),
//             ], 500);
//         }
//     }
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

        // Count by user_type
        $userTypeCounts = User::select('user_type', \DB::raw('count(*) as count'))
            ->where('eligibility_status', 1)
            ->groupBy('user_type')
            ->get()
            ->pluck('count', 'user_type');

        // Total count of all eligible users
        $totalUsers = $users->count();

        return response()->json([
            'message' => 'Users fetched successfully',
            'users' => $users,
            'counts' => [
                'total' => $totalUsers,
                'user' => $userTypeCounts['user'] ?? 0,
                'admin' => $userTypeCounts['admin'] ?? 0,
                'hospital' => $userTypeCounts['hospital'] ?? 0,
            ],
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
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get user by email
        $user = User::where('email', $request->email)->first();

        // Rate limiting - prevent OTP spam
        if ($user->otp_created_at && Carbon::parse($user->otp_created_at)->diffInMinutes(now()) < 5) {
            return response()->json(['message' => 'OTP has already been sent. Please wait before requesting again.'], 429);
        }

        // Generate and store OTP with expiry
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_created_at = now(); // Store timestamp
        $user->save();

        // Queue email for faster response
        Mail::to($user->email)->queue(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully to your email. It expires in 10 minutes.'], 200);
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
// public function fetchNearbyUsers(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'latitude' => 'required|numeric',
//         'longitude' => 'required|numeric',
//         'radius' => 'required|numeric', // Radius in kilometers
//         'min_age' => 'nullable|integer',
//         'max_age' => 'nullable|integer',
//         'blood_type' => 'nullable|string',
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['errors' => $validator->errors()], 422);
//     }

//     $latitude = $request->latitude;
//     $longitude = $request->longitude;
//     $radius = $request->radius;
//     $minAge = $request->min_age;
//     $maxAge = $request->max_age;
//     $bloodType = $request->blood_type;

//     $query = User::select('*')
//         ->selectRaw('(
//             6371 * acos(
//                 cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
//                 sin(radians(?)) * sin(radians(latitude))
//             )
//         ) AS distance', [$latitude, $longitude, $latitude])
//         ->having('distance', '<=', $radius)
//         ->where('user_type', 'user') // Ensure only users are fetched
//         ->orderBy('distance');

//     if (!empty($minAge) && !empty($maxAge)) {
//         $query->whereBetween('age', [$minAge, $maxAge]);
//     } elseif (!empty($minAge)) {
//         $query->where('age', '>=', $minAge);
//     } elseif (!empty($maxAge)) {
//         $query->where('age', '<=', $maxAge);
//     }

//     if (!empty($bloodType)) {
//         $query->where('blood_type', $bloodType);
//     }

//     $nearbyUsers = $query->get();

//     return response()->json(['nearby_users' => $nearbyUsers], 200);
// }

public function fetchNearbyUsers(Request $request)
{
    $validator = Validator::make($request->all(), [
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'radius' => 'required|numeric', // Radius in kilometers
        'min_age' => 'nullable|integer',
        'max_age' => 'nullable|integer',
        'blood_types' => 'nullable|array', // Allow multiple blood types
        'blood_types.*' => 'string', // Ensure each blood type is a string
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $latitude = $request->latitude;
    $longitude = $request->longitude;
    $radius = $request->radius;
    $minAge = $request->min_age;
    $maxAge = $request->max_age;
    $bloodTypes = $request->blood_types; // Now expects an array of blood types

    $query = User::select('*')
        ->selectRaw('(
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ) AS distance', [$latitude, $longitude, $latitude])
        ->having('distance', '<=', $radius)
        ->where('user_type', 'user') // Ensure only users are fetched
        ->orderBy('distance');

    if (!empty($minAge) && !empty($maxAge)) {
        $query->whereBetween('age', [$minAge, $maxAge]);
    } elseif (!empty($minAge)) {
        $query->where('age', '>=', $minAge);
    } elseif (!empty($maxAge)) {
        $query->where('age', '<=', $maxAge);
    }

    if (!empty($bloodTypes)) {
        $query->whereIn('blood_type', $bloodTypes); // Filter users by multiple blood types
    }

    $nearbyUsers = $query->get();

    return response()->json(['nearby_users' => $nearbyUsers], 200);
}


}
