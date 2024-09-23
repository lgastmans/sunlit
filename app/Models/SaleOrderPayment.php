<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleOrderPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['sale_order_id', 'dealer_id', 'amount', 'reference', 'paid_at'];

    protected $dates = ['paid_at'];
}
