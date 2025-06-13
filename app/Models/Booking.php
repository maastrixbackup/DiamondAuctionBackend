<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'bidder_id',
        'room_name',
        'room_type',
        'start_time',
        'date_for_reservation',
        'booking_lot_id',
        'lot_booking_flag',
        'requested_lot_id',
        'lot_requested_flag',
        'timer',
        'timer_status',
    ];

    protected $casts = [
        'booking_lot_id' => 'array',
        // 'requested_lot_id' => 'array',
        // 'lot_booking_flag' => 'boolean',
        // 'lot_requested_flag' => 'boolean',
        // 'timer_status' => 'boolean',
        // 'bid_details' => 'array',
    ];

    // Relationships
    public function bidder()
    {
        return $this->belongsTo(Bidder::class);
    }
}
