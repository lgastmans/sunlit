<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'supplier_id', 'order_number', 'boe_number', 'ordered_at', 'expected_at', 'received_at', 'credit_period', 'amount_usd', 'amount_inr', 'customs_ex_rate', 'se_ex_rate', 'duty_amount', 'social_surcharge', 'igst', 'bank_charges', 'clearing_charges', 'transport_charges', 'se_due_date', 'se_payment_date', 'status', 'user_id'];
    
}
