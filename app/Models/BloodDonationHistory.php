<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodDonationHistory extends Model
{
    use HasFactory;

    protected $table = 'blood_donation_history';

    protected $fillable = ['user_id', 'doctor_id', 'dod', 'status'];

    protected $casts = [
        'user_id' => 'integer',
        'doctor_id' => 'integer',
        'dod' => 'date',
        'status' => 'string',
    ];

    // Relationship: BloodDonationHistory belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class, 'donation_id', 'donation_id');
    }

}
