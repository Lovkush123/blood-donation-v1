<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Table associated with the model
    protected $table = 'events';

    // Primary key of the table
    protected $primaryKey = 'event_id';

    // Fields that can be mass-assigned
    protected $fillable = [
        'event_name',
        'event_type',
        'event_date',
        'location',
        'organizer',
        'description',
        'status',
        'user_id',
        'members', // Added new field
        'concern_person', // Added new field
    ];
}
