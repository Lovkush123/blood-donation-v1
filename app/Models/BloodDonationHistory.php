<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodDonationHistory extends Model
{
    use HasFactory;

    protected $table = 'blood_donation_history';
    protected $fillable = ['user_id', 'doctor_id', 'dod', 'status'];

    // Ensure user_id and doctor_id are cast as integers
    protected $casts = [
        'user_id' => 'integer',
        'doctor_id' => 'integer',
        'dod' => 'date',
        'status' => 'string',
    ];
}
