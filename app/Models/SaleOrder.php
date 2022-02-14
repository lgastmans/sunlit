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
    const BLOCKED = 2;      // changed to Blocked, was Ordered
    const BOOKED = 3;       // changed to Booked, was Confirmed
    const DISPATCHED = 4;   // changed to Dispatched, was Shipped
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
    public function getDisplayBlockedAtAttribute()
    {
        if ($this->blocked_at){
            $dt = Carbon::parse($this->blocked_at);
            return $dt->toFormattedDateString(); 
        } 
        return "";
    }

    public function setBlockedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['blocked_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the confirmed_at date for display Month Day, Year
     */
    public function getDisplayBookedAtAttribute()
    {
        if ($this->booked_at){
            $dt = Carbon::parse($this->booked_at);
            return $dt->toFormattedDateString();  
        }
        return "";
    }

    public function setBookedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['booked_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the shipped_at date for display Month Day, Year
     */
    public function getDisplayDispatchedAtAttribute()
    {
        if ($this->dispatched_at){
            $dt = Carbon::parse($this->dispatched_at);
            return $dt->toFormattedDateString(); 
        } 
        return ""; 
    }

    public function setDispatchedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['dispatched_at'] = $dt->toDateTimeString();  
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
            case SaleOrder::BOOKED:
                $status = '<span class="badge badge-primary-lighten">Booked</span>';
                break;
            case SaleOrder::DISPATCHED:
                $status = '<span class="badge badge-dark-lighten">Dispatched</span>';
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
            SaleOrder::BOOKED => 'Booked',
            SaleOrder::DISPATCHED => 'Dispatched', 
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
