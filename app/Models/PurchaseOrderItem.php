<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_order_id', 'product_id', 'tax_id', 'quantity_ordered', 'quantity_received', 'selling_price'];



    /**
     * Get the order associated with the purchase order item.
     */
    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    
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
