<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['dealer_id', 'warehouse_id', 'order_number', 'order_number_slug', 'status', 'user_id'];
    protected $dates = ['blocked_at', 'booked_at', 'dispatched_at', 'paid_at', 'due_at', 'shipped_at'];
    protected $with = ['dealer', 'warehouse', 'user', 'items'];

    const DRAFT = 1;
    const BLOCKED = 2;      // change to Blocked, was Ordered
    const CONFIRMED = 3;    // change to Booked, was Confirmed
    const SHIPPED = 4;      // change to Dispatched, was Shipped
    // const CUSTOMS = 5;
    // const CLEARED = 6;
    const DELIVERED = 7;    // remove 

    /**
     * Get the items associated with the sale order.
     */
    public function items()
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    /**
     * Get the dealer associated with the sale order.
     */
    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * Get the warehouse associated with the sale order.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the user associated with the sale order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Returns the ordered_at date for display Month Day, Year
     */
    public function getDisplayOrderedAtAttribute()
    {
        if ($this->ordered_at){
            $dt = Carbon::parse($this->ordered_at);
            return $dt->toFormattedDateString(); 
        } 
        return "";
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
        if ($this->confirmed_at){
            $dt = Carbon::parse($this->confirmed_at);
            return $dt->toFormattedDateString();  
        }
        return "";
    }

    public function setConfirmedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['confirmed_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the shipped_at date for display Month Day, Year
     */
    public function getDisplayShippedAtAttribute()
    {
        if ($this->shipped_at){
            $dt = Carbon::parse($this->shipped_at);
            return $dt->toFormattedDateString(); 
        } 
        return ""; 
    }

    public function setShippedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['shipped_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the due_at date for display Month Day, Year
     */
    public function getDisplayDueAtAttribute()
    {
        if ($this->due_at){
            $dt = Carbon::parse($this->due_at);
            return $dt->toFormattedDateString(); 
        } 
        return ""; 
    }

    public function setDueAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['due_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the customs_at date for display Month Day, Year
     */
    public function getDisplayDeliveredAtAttribute()
    {
        if ($this->delivered_at){
            $dt = Carbon::parse($this->delivered_at);
            return $dt->toFormattedDateString();  
        } 
        return "";
    }

    public function setDeliveredAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['delivered_at'] = $dt->toDateTimeString();  
    }


    public function getDisplayStatusAttribute()
    {
        switch ($this->status)
        {
            case SaleOrder::DRAFT:
                $status =  '<span class="badge badge-secondary-lighten">Draft</span>';
                break;
            case SaleOrder::BLOCKED:
                $status = '<span class="badge badge-info-lighten">Blocked</span>';
                break;
            case SaleOrder::CONFIRMED:
                $status = '<span class="badge badge-primary-lighten">Confirmed</span>';
                break;
            case SaleOrder::SHIPPED:
                $status = '<span class="badge badge-dark-lighten">Shipped</span>';
                break;
            case SaleOrder::DELIVERED:
                $status = '<span class="badge badge-success-lighten">Delivered</span>';
                break;
            default:
                $status = '<span class="badge badge-error-lighten">Unknown</span>';
        }
        return $status;
    }

    public static function getStatusList()
    {
        return [
            SaleOrder::DRAFT => 'Draft', 
            SaleOrder::BLOCKED => 'Blocked', 
            SaleOrder::CONFIRMED => 'Confirmed',
            SaleOrder::SHIPPED => 'Shipped', 
            SaleOrder::DELIVERED => 'Delivered'
        ];
    }


    public function isOverdue()
    {
        if (Carbon::now()->greaterThan(Carbon::parse( $this->due_at))){
            return true;
        }
        return false;
    }

    public function getOrderedDaysAgoAttribute()
    {
        return Carbon::parse( $this->ordered_at)->diffForHumans();
    }


    public function scopeOrdered($query)
    {
        return $query->where('status', '>=', SaleOrder::BLOCKED);
    }

    /* * Retrieve orders with status DELIVERED
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', SaleOrder::DELIVERED);
    }    


}
