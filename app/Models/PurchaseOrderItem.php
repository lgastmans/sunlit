<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = ['purchase_order_id', 'product_id', 'tax_id', 'quantity_confirmed', 'buying_price'];



    /**
     * Get the order associated with the purchase order item.
     */
    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    } 

    public function getTotalPriceAttribute()
    {
        $total = $this->buying_price * $this->quantity_confirmed;
        return  $total;
    }
}
