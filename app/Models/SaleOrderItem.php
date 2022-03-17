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
    public function sale_order()
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

    /**
     * Returns the Taxable Value
     */
    public function getTaxableValueAttribute()
    {
        return number_format($this->quantity_ordered * $this->selling_price, 2);
    }

    /**
     * Returns the Tax Amount IGST
     */
    public function getTaxAmountAttribute()
    {
        return number_format(($this->quantity_ordered * $this->selling_price) * ($this->tax/100), 2);
    }

}
