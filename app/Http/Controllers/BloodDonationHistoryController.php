<?php

namespace App\Http\Controllers;

use App\Models\BloodDonationHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
class BloodDonationHistoryController extends Controller
{
    // public function index()
    // {
    //     // Fetch all donation histories with user details
    //     $histories = BloodDonationHistory::with('user')->get();
    //     return response()->json($histories);
    // }

    public function index()
    {
        // Fetch all donation histories with user and donation details
        $histories = BloodDonationHistory::with(['user', 'donation'])->get();
    
        // Get current date
        $now = Carbon::now();
    
        // Calculate date ranges
        $lastWeek = $now->copy()->subWeek();
        $lastMonth = $now->copy()->subMonth();
        $lastYear = $now->copy()->subYear();
    
        // Count donations within each range
        $weeklyCount = BloodDonationHistory::where('dod', '>=', $lastWeek)->count();
        $monthlyCount = BloodDonationHistory::where('dod', '>=', $lastMonth)->count();
        $yearlyCount = BloodDonationHistory::where('dod', '>=', $lastYear)->count();
    
        return response()->json([
            'donations' => $histories,
            'stats' => [
                'weekly_count' => $weeklyCount,
                'monthly_count' => $monthlyCount,
                'yearly_count' => $yearlyCount
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
            'doctor_id' => 'required|integer',
            'dod' => 'required|date',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        try {
            $history = BloodDonationHistory::create($validated);
            return response()->json([
                'message' => 'Record created successfully',
                'data' => $history
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // public function show($id)
    // {
    //     $history = BloodDonationHistory::with('user')->find($id);

    //     if (!$history) {
    //         return response()->json(['error' => 'Record not found'], 404);
    //     }

    //     return response()->json($history);
    // }
    public function show($id)
    {
        // Include user and donation details
        $history = BloodDonationHistory::with(['user', 'donation'])->find($id);

        if (!$history) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        return response()->json($history);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|integer|exists:users,user_id',
            'doctor_id' => 'sometimes|integer',
            'dod' => 'sometimes|date',
            'status' => 'sometimes|in:pending,approved,rejected',
        ]);

        $history = BloodDonationHistory::find($id);

        if (!$history) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $history->update($validated);

        return response()->json([
            'message' => 'Record updated successfully',
            'data' => $history
        ]);
    }

    public function destroy($id)
    {
        $history = BloodDonationHistory::find($id);

        if (!$history) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $history->delete();

        return response()->json(['message' => 'Record deleted successfully'], 204);
    }

    public function getDonationHistory(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
        ]);

        try {
            $user = User::with('bloodDonationHistories')->where('user_id', $validated['user_id'])->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found.'
                ], 404);
            }

            return response()->json([
                'message' => 'User details and donation history retrieved successfully.',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
