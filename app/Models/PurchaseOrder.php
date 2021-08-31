<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    const Draft = 1, 
        Ordered = 2,
        Confirmed = 3,
        Shipped = 4,
        Customs = 5,
        Cleared = 6,
        Received = 7;

    protected $dates = ['ordered_at', 'expected_at', 'received_at', 'se_due_date', 'se_payment_date',];

    protected $fillable = ['warehouse_id', 'supplier_id', 'order_number', 'boe_number', 'ordered_at', 'expected_at', 'received_at', 'credit_period', 'amount_usd', 'amount_inr', 'customs_ex_rate', 'se_ex_rate', 'duty_amount', 'social_surcharge', 'igst', 'bank_charges', 'clearing_charges', 'transport_charges', 'se_due_date', 'se_payment_date', 'status', 'user_id'];

    /**
     * Get the warehouse associated with the product.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the supplier associated with the product.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user associated with the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }        
}
