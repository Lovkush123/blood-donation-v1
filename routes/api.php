<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BloodDonationHistoryController;
use App\Http\Controllers\DonationController;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('registerer', [AuthController::class, 'registerer']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::patch('update/{userId}', [AuthController::class, 'update']);
Route::get('/user/{id}', [AuthController::class, 'fetchUser']);
Route::get('users', [AuthController::class, 'fetchAllUsers']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::post('fetchnear', [AuthController::class, 'fetchNearbyUsers']);
// Subscription Routes
Route::get('subscription', [SubscriptionController::class, 'index']);
Route::post('subscription', [SubscriptionController::class, 'store']);
Route::get('subscription/{subscription}', [SubscriptionController::class, 'show']);
Route::put('subscription/{subscription}', [SubscriptionController::class, 'update']);
Route::delete('subscription/{subscription}', [SubscriptionController::class, 'destroy']);

//make event routes

Route::get('events', [EventController::class, 'index']);
Route::post('events', [EventController::class, 'store']);
Route::get('events/{id}', [EventController::class, 'show']);
Route::put('events/{id}', [EventController::class, 'update']);
Route::delete('events/{id}', [EventController::class, 'destroy']);
Route::get('events/user/{user_id}', [EventController::class, 'fetchByUserId']);


//donation routes 

Route::post('donations', [DonationController::class, 'store']); // Route to create donation
Route::get('donations/user/{userId}', [DonationController::class, 'fetchByUserId']); // Route to fetch donations by user ID
// Route::post('donors/check', [DonationController::class, 'fetchDonor']); // Route to check if a donor
Route::get('/eligible', [DonationController::class, 'fetchDonorsEligibleForDonation']);


Route::get('/blood-donations', [BloodDonationHistoryController::class, 'index']);
Route::post('/blood-donations', [BloodDonationHistoryController::class, 'store']);
Route::get('blood-donations/{bloodDonationHistory}', [BloodDonationHistoryController::class, 'show']);
Route::put('blood-donations/{bloodDonationHistory}', [BloodDonationHistoryController::class, 'update']);
Route::delete('blood-donations/{bloodDonationHistory}', [BloodDonationHistoryController::class, 'destroy']);
Route::get('/donation-history', [BloodDonationHistoryController::class, 'getDonationHistory']);




