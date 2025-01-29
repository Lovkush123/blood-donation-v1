<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DonationController extends Controller
{
    // Display all donations based on user_id
    public function fetchByUserId($userId)
    {
        // Fetch donations for the specified user
        $donations = Donation::where('user_id', $userId)->get();
        return response()->json($donations);
    }

    // Create a new donation
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'user_id' => 'required|integer',
            'donation_date' => 'required|date',
            'quantity' => 'required|integer',
            'donation_center' => 'required|string',
            'credit_point' => 'required|integer',
        ]);

        // Create a new donation record
        $donation = Donation::create($request->all());
        return response()->json($donation, 201); // Return created donation with 201 status
    }

    // Fetch donors who can donate based on a 30-day gap
    // public function fetchDonor(Request $request)
    // {
    //     // Validate the incoming data
    //     $request->validate([
    //         'user_id' => 'required|integer',
    //     ]);

    //     // Fetch the most recent donation of the user
    //     $latestDonation = Donation::where('user_id', $request->user_id)
    //         ->latest('donation_date') // Get the latest donation by donation_date
    //         ->first();

    //     if ($latestDonation) {
    //         // Calculate the 30-day gap
    //         $lastDonationDate = Carbon::parse($latestDonation->donation_date);
    //         $today = Carbon::now();
    //         $daysDifference = $lastDonationDate->diffInDays($today);

    //         // If 30 days have passed, allow donation
    //         if ($daysDifference >= 30) {
    //             return response()->json([
    //                 'message' => 'Donor can donate blood again.',
    //                 'can_donate' => true
    //             ]);
    //         } else {
    //             $daysRemaining = 30 - $daysDifference;
    //             return response()->json([
    //                 'message' => 'Donor cannot donate yet.',
    //                 'can_donate' => false,
    //                 'days_remaining' => $daysRemaining
    //             ]);
    //         }
    //     } else {
    //         // If no previous donations exist
    //         return response()->json([
    //             'message' => 'Donor can donate blood.',
    //             'can_donate' => true
    //         ]);
    //     }
    // }

    public function fetchDonorsEligibleForDonation()
    {
        // Fetch users who can donate blood (those who have not donated in the last 30 days)
        $eligibleDonors = Donation::select('user_id', 'donation_date', 'quantity', 'donation_center')
            ->selectRaw('MAX(donation_date) as latest_donation_date')
            ->groupBy('user_id', 'donation_date', 'quantity', 'donation_center')
            ->havingRaw('DATEDIFF(CURDATE(), MAX(donation_date)) >= 30')
            ->get();
    
        // Return the list of users eligible for donation
        return response()->json([
            'message' => 'Donors eligible to donate blood.',
            'eligible_donors' => $eligibleDonors
        ]);
    }
    
}
