<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try { 
            $subscription = Subscription::all();
            return response()->json($subscription);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve subscription', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'subscription_title' => 'required|string|max:255',
                'subscription_desc' => 'required|string',
                'subscription_price' => 'required|numeric',
                'subscription_image' => 'required|string|max:255',
                'subscription_percentage' => 'required|numeric|max:100',
            ]);

            $subscription = Subscription::create($validated);
            return response()->json($subscription, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create subscription', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        try {
            return response()->json($subscription);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Subscription not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve subscription', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        try {
            $validated = $request->validate([
                'subscription_title' => 'sometimes|required|string|max:255',
                'subscription_desc' => 'sometimes|required|string',
                'subscription_price' => 'sometimes|required|numeric',
                'subscription_image' => 'sometimes|required|string|max:255',
                'subscription_percentage' => 'sometimes|required|numeric|max:100',
            ]);

            $subscription->update($validated);
            return response()->json($subscription);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update subscription', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        try {
            $subscription->delete();
            return response()->json(['message' => 'Subscription deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete subscription', 'message' => $e->getMessage()], 500);
        }
    }
}
