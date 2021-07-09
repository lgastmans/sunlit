<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Get the state associated with the supplier.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the products associated with the supplier.
     */
    public function products()
    {
        return $this->HasMany(Product::class);
    }
}
