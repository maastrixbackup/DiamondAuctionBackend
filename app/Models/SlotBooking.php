<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'slot_id',
        'start_time',
        'date_for_reservation',
        'bidder_id',
        'status',
    ];
}
