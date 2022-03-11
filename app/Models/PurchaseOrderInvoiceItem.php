<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderInvoiceItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['purchase_order_id', 'product_id','quantity_shipped', 'selling_price'];
    protected $casts = [
        'charges' => 'array',
    ];


    /**
     * Get the warehouse associated with the purchase order.
     */
    public function purchase_order_invoice()
    {
        return $this->belongsTo(PurchaseOrderInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    } 

    public function getTotalPriceAttribute()
    {
        $total = $this->buying_price * $this->quantity_shipped;
        return  $total;
    }


}
