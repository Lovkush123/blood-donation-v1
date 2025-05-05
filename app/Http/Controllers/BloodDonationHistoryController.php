<?php

namespace App\Http\Controllers;

use App\Models\BloodDonationHistory;
use Illuminate\Http\Request;

class BloodDonationHistoryController extends Controller
{
    public function index()
    {
        return response()->json(BloodDonationHistory::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer', // Removed foreign key validation
            'doctor_id' => 'required|integer', // Removed foreign key validation
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

    public function show($id)
    {
        $history = BloodDonationHistory::find($id);

        if (!$history) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        return response()->json($history);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|integer', // Removed foreign key validation
            'doctor_id' => 'sometimes|integer', // Removed foreign key validation
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
        'user_id' => 'required|integer|exists:users,id',
    ]);

    try {
        $user = User::with('bloodDonationHistory')->find($validated['user_id']);

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
