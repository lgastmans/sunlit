<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleOrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Get the order associated with the item.
     */
    public function order()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    /**
     * Get the product associated with the item.
     */
    public function product()
    {
        return $this->belongsTo(SaleOrder::class);
    }
}
