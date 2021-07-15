<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    /**
     * Get the state associated with the supplier.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
