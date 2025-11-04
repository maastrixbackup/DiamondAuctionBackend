<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulkBidding extends Model
{
    use HasFactory, SoftDeletes;


    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }
}
