<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkBidding extends Model
{
    use HasFactory;

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }
}
