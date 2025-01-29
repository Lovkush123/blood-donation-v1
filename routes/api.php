<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\EventController;

use App\Http\Controllers\DonationController;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::patch('update/{userId}', [AuthController::class, 'update']);
Route::get('/user/{id}', [AuthController::class, 'fetchUser']);
Route::get('users', [AuthController::class, 'fetchAllUsers']);

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
