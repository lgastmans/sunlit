<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PurchaseOrderInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    const DRAFT = 1;
    const ORDERED = 2;
    const CONFIRMED = 3;
    const SHIPPED = 4;
    const CUSTOMS = 5;
    const CLEARED = 6;
    const RECEIVED = 7;
    const PAID = 8;

    protected $dates = ['due_at', 'shipped_at', 'customs_at', 'cleared_at',  'received_at', 'paid_at'];
    protected $with = ['user'];



    /**
     * Get the user associated with the purchase order invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    } 


    /**
     * Get the purchase order associated with the invoice.
     */
    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the items associated with the purchase order invoice.
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderInvoiceItem::class);
    }


    public function getLandedCost()
    {
        $landed_cost = $this->amount_inr_customs + $this->customs_duty + $this->social_welfare_surcharge + $this->bank_and_transport_charges;
        return $landed_cost;
    }


    public function updateItemsBuyingPrice($invoice_id)
    {
        $invoice = PurchaseOrderInvoice::with('items')->find($invoice_id);
        $fx_rate = $invoice->landed_cost / $invoice->amount_usd;
        foreach($invoice->items as $item){
            $item->buying_price_inr = $item->buying_price * $fx_rate;
            $item->update();
        }
    }

    public function updateItemsPaidPrice($invoice_id)
    {
        $invoice = PurchaseOrderInvoice::with('items')->find($invoice_id);
        foreach($invoice->items as $item){
            $item->paid_price_inr = $item->buying_price * $invoice->paid_exchange_rate;
            $item->update();
        }
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
    public function getDisplayCustomsAtAttribute()
    {
        if ($this->customs_at){
            $dt = Carbon::parse($this->customs_at);
            return $dt->toFormattedDateString(); 
        } 
        return "";    
    }

    public function setCustomsAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['customs_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the customs_at date for display Month Day, Year
     */
    public function getDisplayClearedAtAttribute()
    {
        if ($this->cleared_at){
            $dt = Carbon::parse($this->cleared_at);
            return $dt->toFormattedDateString(); 
        } 
        return "";    
    }

    public function setClearedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['cleared_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the customs_at date for display Month Day, Year
     */
    public function getDisplayReceivedAtAttribute()
    {
        if ($this->received_at){
            $dt = Carbon::parse($this->received_at);
            return $dt->toFormattedDateString(); 
        } 
        return "";    
    }

    public function setReceivedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['received_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the paid_at date for display Month Day, Year
     */
    public function getDisplayPaidAtAttribute()
    {
        if ($this->paid_at){
            $dt = Carbon::parse($this->paid_at);
            return $dt->toFormattedDateString(); 
        } 
        return "";    
    }

    public function setPaidAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['paid_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the amount paid with customs exchange rate
     */
    public function getAmountInrCustomsAttribute()
    {
        return $this->amount_usd * $this->customs_exchange_rate;
    }

    /**
     * Returns the landed cost for an order
     */
    public function getLandedCostAttribute()
    {
        return $this->amount_inr + $this->customs_duty + $this->social_welfare_surcharge + $this->bank_and_tranport_charge;
    }


    public function getDisplayStatusAttribute()
    {
        switch ($this->status)
        {
            case PurchaseOrderInvoice::DRAFT:
                $status =  '<span class="badge badge-secondary-lighten">Draft</span>';
                break;
            case PurchaseOrderInvoice::ORDERED:
                $status = '<span class="badge badge-info-lighten">Ordered</span>';
                break;
            case PurchaseOrderInvoice::CONFIRMED:
                $status = '<span class="badge badge-primary-lighten">Confirmed</span>';
                break;
            case PurchaseOrderInvoice::SHIPPED:
                $status = '<span class="badge badge-dark-lighten">Shipped</span>';
                break;
            case PurchaseOrderInvoice::CUSTOMS:
                $status = '<span class="badge badge-warning-lighten">Customs</span>';
                break;
            case PurchaseOrderInvoice::CLEARED:
                $status = '<span class="badge badge-light-lighten">Cleared</span>';
                break;
            case PurchaseOrderInvoice::RECEIVED:
                $status = '<span class="badge badge-success-lighten">Received</span>';
                break;
            case PurchaseOrderInvoice::PAID:
                $status = '<span class="badge badge-success-lighten">Paid</span>';
                break;
            default:
                $status = '<span class="badge badge-error-lighten">Unknown</span>';
        }
        return $status;
    }

    public static function getStatusList()
    {
        return [
            PurchaseOrderInvoice::DRAFT => 'Draft', 
            PurchaseOrderInvoice::ORDERED => 'Ordered', 
            PurchaseOrderInvoice::CONFIRMED => 'Confirmed',
            PurchaseOrderInvoice::SHIPPED => 'Shipped', 
            PurchaseOrderInvoice::CUSTOMS => 'Customs', 
            PurchaseOrderInvoice::CLEARED => 'Cleared',
            PurchaseOrderInvoice::RECEIVED => 'Received',
            PurchaseOrderInvoice::PAID => 'Paid'
        ];
    }
}
