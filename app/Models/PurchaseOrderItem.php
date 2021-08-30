<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_order_id', 'product_id', 'tax_id', 'quantity_ordered', 'quantity_received', 'selling_price'];
}
