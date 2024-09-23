<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleOrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['sale_order_id', 'product_id', 'quantity_ordered', 'selling_price', 'tax'];

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
        $total = ($this->selling_price + ($this->selling_price * $this->tax / 100)) * $this->quantity_ordered;

        return $total;
    }

    /**
     * Returns the Taxable Value
     */
    public function getTaxableValueAttribute()
    {
        return $this->quantity_ordered * $this->selling_price;
    }

    /**
     * Returns the Tax Amount IGST
     */
    public function getTaxAmountAttribute()
    {
        return ($this->quantity_ordered * $this->selling_price) * ($this->tax / 100);
    }
}
