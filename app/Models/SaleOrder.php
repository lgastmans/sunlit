<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['ordered_at', 'confirmed_at', 'received_at', 'paid_at', 'due_at', 'shipped_at'];
    protected $with = ['dealer', 'user'];

    const DRAFT = 1;
    const ORDERED = 2;
    const CONFIRMED = 3;
    const SHIPPED = 4;
    // const CUSTOMS = 5;
    // const CLEARED = 6;
    const RECEIVED = 7;


    /**
     * Get the dealer associated with the sale order.
     */
    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
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
     * Returns the shipped_at date for display Month Day, Year
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

    /**
     * Returns the due_at date for display Month Day, Year
     */
    public function getDisplayDueAtAttribute()
    {
        $dt = Carbon::parse($this->due_at);
        return $this->attributes['due_at'] = $dt->toFormattedDateString();  
    }

    public function setDueAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['due_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the customs_at date for display Month Day, Year
     */
    public function getDisplayReceivedAtAttribute()
    {
        $dt = Carbon::parse($this->received_at);
        return $this->attributes['received_at'] = $dt->toFormattedDateString();  
    }

    public function setReceivedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['received_at'] = $dt->toDateTimeString();  
    }


    public function getDisplayStatusAttribute()
    {
        switch ($this->status)
        {
            case SaleOrder::DRAFT:
                $status =  '<span class="badge badge-secondary-lighten">Draft</span>';
                break;
            case SaleOrder::ORDERED:
                $status = '<span class="badge badge-info-lighten">Ordered</span>';
                break;
            case SaleOrder::CONFIRMED:
                $status = '<span class="badge badge-primary-lighten">Confirmed</span>';
                break;
            case SaleOrder::SHIPPED:
                $status = '<span class="badge badge-dark-lighten">Shipped</span>';
                break;
            case SaleOrder::RECEIVED:
                $status = '<span class="badge badge-success-lighten">Received</span>';
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
            SaleOrder::ORDERED => 'Ordered', 
            SaleOrder::CONFIRMED => 'Confirmed',
            SaleOrder::SHIPPED => 'Shipped', 
            SaleOrder::RECEIVED => 'Received'
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
        return $query->where('status', '>=', SaleOrder::ORDERED);
    }

    /* * Retrieve orders with status RECEIVED
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReceived($query)
    {
        return $query->where('status', SaleOrder::RECEIVED);
    }    


}
