<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    const DRAFT = 1;
    const ORDERED = 2;
    const CONFIRMED = 3;
    const SHIPPED = 4;
    const CUSTOMS = 5;
    const CLEARED = 6;
    const RECEIVED = 7;

    protected $dates = ['ordered_at', 'confirmed_at', 'received_at', 'se_due_date', 'se_payment_date',];

    protected $fillable = ['warehouse_id', 'supplier_id', 'order_number', 'boe_number', 'ordered_at', 'expected_at', 'received_at', 'credit_period', 'amount_usd', 'amount_inr', 'customs_ex_rate', 'se_ex_rate', 'duty_amount', 'social_surcharge', 'igst', 'bank_charges', 'clearing_charges', 'transport_charges', 'se_due_date', 'se_payment_date', 'status', 'user_id'];

    /**
     * Get the warehouse associated with the purchase order.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the supplier associated with the purchase order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user associated with the purchase order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    } 
    
    /**
     * Get the items associated with the purchase order.
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    } 

    /**
     * Returns the ordered_at date for display Month Day, Year
     */
    public function getDisplayOrderedAtAttribute()
    {
        $dt = Carbon::parse($this->ordered_at);
        return $this->attributes['ordered_at'] = $dt->toFormattedDateString();  
    }

    public function setOrderedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['ordered_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the confirmed_at date for display Month Day, Year
     */
    public function getDisplayConfirmedAtAttribute()
    {
        $dt = Carbon::parse($this->confirmed_at);
        return $this->attributes['confirmed_at'] = $dt->toFormattedDateString();  
    }

    public function setConfirmedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['confirmed_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the ordered_at date for display Month Day, Year
     */
    public function getDisplayShippedAtAttribute()
    {
        $dt = Carbon::parse($this->shipped_at);
        return $this->attributes['shipped_at'] = $dt->toFormattedDateString();  
    }

    public function setShippedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['shipped_at'] = $dt->toDateTimeString();  
    }






    /* * Retrieve orders with status RECEIVED
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReceived($query)
    {
        return $query->where('status', PurchaseOrder::RECEIVED);
    }    
}
