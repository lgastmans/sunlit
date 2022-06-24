<?php

namespace App\Models;

use Carbon\Carbon;
use NumberFormatter;
//use Spatie\Activitylog\LogOptions;
//use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
//    use LogsActivity;

    protected $fillable = ['dealer_id', 'warehouse_id', 'order_number', 'order_number_slug', 'status', 'user_id',
            'shipping_state_id', 'shipping_company', 'shipping_address', 'shipping_address2', 'shipping_city', 'shipping_zip_code', 'shipping_gstin', 'shipping_contact_person', 'shipping_phone'];
    protected $dates = ['blocked_at', 'booked_at', 'dispatched_at', 'paid_at', 'due_at', 'shipped_at'];
    protected $with = ['dealer', 'warehouse', 'user', 'items', 'state'];
    //protected static $recordEvents = ['created','updated','deleted'];

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

    /*
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['dealer.company', 'warehouse.name,','order_number', 'status', 'user.name', 'blocked_at','booked_at','dispatched_at'])
        ->dontLogIfAttributesChangedOnly(['updated_at','shipping_company','shipping_state_id','shipping_gstin','shipping_phone','shipping_city','shipping_address2','shipping_address','shipping_contact_person','shipping_zip_code']);
    }
    */
    
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
     * Get the state associated with the dealer.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'shipping_state_id');
    }

    public function canDispatch()
    {
        $response=array();
        $response['success'] = 1;
        // $response['item'] = 'test';
        //return response()->json($response);
        
        $inventory = new Inventory;

        foreach ($this->items as $item)
        {
            if (!$inventory->hasStock($this->warehouse_id, $item->product_id, $item->quantity_ordered))
            {
                $response['success'] = 0;
                $response['item'] = "Part number ".$item->product->part_number;
            }
        }
        
        return $response;
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

        $this->total = round($this->total);

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
        if ($this->booked_at)
        {
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
        if ($this->dispatched_at)
        {
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
        if ($this->due_at)
        {
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
        if ($this->delivered_at)
        {
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
            SaleOrder::DISPATCHED => 'Dispatched'
            //SaleOrder::DELIVERED => 'Delivered'
        ];
    }


    public function isOverdue()
    {
        if (Carbon::now()->greaterThan(Carbon::parse( $this->due_at)))
        {
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
