<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleOrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $with = ['product', 'product.tax'];

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
        return $this->belongsTo(Product::class);
    }

    public function getTotalPriceAttribute()
    {
        $total = ($this->selling_price + ($this->selling_price * $this->tax/100)) * $this->quantity_ordered;
        return  $total;
    }

}