<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderInvoiceItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['purchase_order_id', 'product_id', 'quantity_shipped', 'selling_price'];

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

        return $total;
    }

    public function updateInvoiceItemCharges($invoice_item_id)
    {
        $item = PurchaseOrderInvoiceItem::find($invoice_item_id);

        $charges = $item->charges;

        $item->customs_duty = $item->buying_price * $item->quantity_shipped * ($charges['customs_duty'] / 100);

        $item->social_welfare_surcharge = $item->customs_duty * ($charges['social_welfare_surcharge'] / 100);

        $item->igst = (
            ($item->buying_price * $item->quantity_shipped)
            + $item->customs_duty
            + $item->social_welfare_surcharge
        ) * ($charges['igst'] / 100);

        $item->update();

    }
}
