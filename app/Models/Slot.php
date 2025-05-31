<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'start_time',
        'end_time',
        'date_for_reservation',
        'slot_status',
        'reserved_by',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // public function lots()
    // {
    //     return $this->hasMany(Lot::class, 'slot_id');
    // }
}
