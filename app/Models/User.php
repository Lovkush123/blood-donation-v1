<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id'; // Use custom primary key
    protected $fillable = [
        'full_name',
        'username',  // Add username to the fillable array
        'email',
        'password',
        'phone_number',
        'address',
        'latitude',
        'longitude',
        'date_of_birth',
        'age',
        'blood_type',
        'last_donation_date',
        'eligibility_status',
        'credit_points',
        'token',
        'user_type',
        'status',
        'count'
    ];

    // Hide password and token when converting to array or JSON
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Cast password field to ensure it is hashed
    protected $casts = [
        'password' => 'hashed',
    ];
}