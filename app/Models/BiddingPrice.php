<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiddingPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'lot_id',
        'price',
        'bidding_time',
    ];
}
