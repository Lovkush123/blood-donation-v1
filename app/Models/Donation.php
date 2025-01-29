<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    // Specify the table if it's not the plural form of the model name
    protected $table = 'donations';

    // Define the primary key if it's not 'id'
    protected $primaryKey = 'donation_id';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'donation_date',
        'quantity',
        'donation_center',
        'notes',
        'credit_point',
    ];

    // Disable the timestamps if you don't want to use them
    public $timestamps = true; // Default is true
}
