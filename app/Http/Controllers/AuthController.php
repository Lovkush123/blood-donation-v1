<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    
    // Login using username and password
    public function login(Request $request)
    {
        try {
            // Validate request input
            $request->validate([
                'username' => 'required|string', // Validate username instead of email
                'password' => 'required|string',
            ]);
    
            // Find user by username
            $user = User::where('username', $request->username)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
    
            // Return a success response with user details
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
            ]);
    
        } catch (\Exception $e) {
            // Catch any exception and return a response with error message
            return response()->json([
                'error' => 'Login failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    // Register a new user
    public function register(Request $request)
{
    try {
        // Validate the incoming request
        $request->validate([
            'full_name' => 'required|string',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'user_type' => 'required|string|in:admin,user,hospital',
            'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'address' => 'required|string',
            'date_of_birth' => 'required|date|before:today',
            'phone_number' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Calculate age based on date_of_birth
        $dateOfBirth = new \DateTime($request->date_of_birth);
        $today = new \DateTime();
        $age = $today->diff($dateOfBirth)->y;

        // Create a new user with hashed password
        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'blood_type' => $request->blood_type,
            'age' => $age,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'phone_number' => $request->phone_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Return a success response with the user
        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
        ]);
    } catch (\Exception $e) {
        // Handle exceptions and return an error response
        return response()->json([
            'error' => 'Registration failed',
            'message' => $e->getMessage(),
        ], 500);
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

}
