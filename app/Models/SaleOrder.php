<?php

namespace App\Models;

use Carbon\Carbon;
use NumberFormatter;
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
     * calculated fields for Sales Order
     */
    var $sub_total = 0;
    var $tax_total = 0;
    var $transport_total = 0;
    var $tax_total_half = 0;
    var $total = 0;
    var $total_spellout = '';

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
     * 
     */
    public function calculateTotals()
    {
        $tax = 0;

        $this->sub_total = 0;
        $this->tax_total = 0;
        $this->transport_total = 0;
        $this->total = 0;

        /**
         * calculate the totals
         */
        foreach ($this->items as $item)
        {
            $this->sub_total += $item->quantity_ordered * $item->selling_price;
            $this->tax_total += ($item->quantity_ordered * $item->selling_price) * ($item->tax / 100);

            $tax = $item->tax;
        }

        /**
         * add the Transport Charges to the totals
         */
        $this->sub_total += $this->transport_charges;
        $this->transport_total = $this->transport_charges + ($this->transport_charges * $tax / 100);
        $this->tax_total += $this->transport_charges * $tax / 100;

        $this->total = $this->sub_total + $this->tax_total;

        /**
         * for SGST and CGST
         */
        $this->tax_total_half = $this->tax_total/2;

        $this->total_spellout = $this->expandAmount($this->total);


        $this->sub_total = number_format($this->sub_total, 2);
        $this->tax_total = number_format($this->tax_total, 2);
        $this->transport_total = number_format($this->transport_total, 2);
        $this->tax_total_half = number_format($this->tax_total_half, 2);
        $this->total = number_format($this->total, 2);

        return true;

    }


    static function expandAmount($amount) {

        $fmt = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        if (strpos($amount,'.') !== false) {

            $numwords = explode('.',$amount);

            if (intval($numwords[1]) > 0)
                $res = $fmt->format($numwords[0]).' and paise '.$fmt->format($numwords[1]).' only';
            else
                $res = $fmt->format((int)$numwords[0]).' only';
        }
        else  {
            $res = $fmt->format($amount).' only';
        }
        $res = 'INR '.$res;

        return ucfirst($res);
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
