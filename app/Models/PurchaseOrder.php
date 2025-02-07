<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
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

    protected $fillable = ['warehouse_id', 'supplier_id', 'order_number', 'order_number_slug', 'boe_number', 'ordered_at', 'expected_at', 'received_at', 'credit_period', 'amount_usd', 'amount_inr', 'customs_ex_rate', 'se_ex_rate', 'duty_amount', 'social_surcharge', 'igst', 'bank_charges', 'clearing_charges', 'transport_charges', 'se_due_date', 'se_payment_date', 'status', 'user_id'];

    protected $with = ['warehouse', 'supplier', 'user', 'invoices'];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'received_at' => 'datetime',
            'paid_at' => 'datetime',
            'due_at' => 'datetime',
            'shipped_at' => 'datetime',
            'customs_at' => 'datetime',
            'cleared_at' => 'datetime',
        ];
    }

    /**
     * Get the warehouse associated with the purchase order.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the supplier associated with the purchase order.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user associated with the purchase order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the items associated with the purchase order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get the items associated with the purchase order.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(PurchaseOrderInvoice::class);
    }

    /**
     * Returns the ordered_at date for display Month Day, Year
     */
    public function getDisplayOrderedAtAttribute()
    {
        if ($this->ordered_at) {
            $dt = Carbon::parse($this->ordered_at);

            return $dt->toFormattedDateString();
        }

        return '';
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
        if ($this->confirmed_at) {
            $dt = Carbon::parse($this->confirmed_at);

            return $dt->toFormattedDateString();
        }

        return '';
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
        if ($this->shipped_at) {
            $dt = Carbon::parse($this->shipped_at);

            return $dt->toFormattedDateString();
        }

        return '';
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
        if ($this->due_at) {
            $dt = Carbon::parse($this->due_at);

            return $dt->toFormattedDateString();
        }

        return '';
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
        if ($this->customs_at) {
            $dt = Carbon::parse($this->customs_at);

            return $dt->toFormattedDateString();
        }

        return '';
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
        if ($this->cleared_at) {
            $dt = Carbon::parse($this->cleared_at);

            return $dt->toFormattedDateString();
        }

        return '';
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
        if ($this->received_at) {
            $dt = Carbon::parse($this->received_at);

            return $dt->toFormattedDateString();
        }

        return '';
    }

    public function setReceivedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['received_at'] = $dt->toDateTimeString();
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
        switch ($this->status) {
            case PurchaseOrder::DRAFT:
                $status = '<span class="badge badge-secondary-lighten">Draft</span>';
                break;
            case PurchaseOrder::ORDERED:
                $status = '<span class="badge badge-info-lighten">Ordered</span>';
                break;
            case PurchaseOrder::CONFIRMED:
                $status = '<span class="badge badge-primary-lighten">Confirmed</span>';
                break;
            case PurchaseOrder::SHIPPED:
                $status = '<span class="badge badge-dark-lighten">Shipped</span>';
                break;
            case PurchaseOrder::CUSTOMS:
                $status = '<span class="badge badge-warning-lighten">Customs</span>';
                break;
            case PurchaseOrder::CLEARED:
                $status = '<span class="badge badge-light-lighten">Cleared</span>';
                break;
            case PurchaseOrder::RECEIVED:
                $status = '<span class="badge badge-success-lighten">Received</span>';
                break;
            case PurchaseOrder::PAID:
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
            //PurchaseOrder::DRAFT => 'Draft',
            PurchaseOrder::ORDERED => 'Ordered',
            PurchaseOrder::CONFIRMED => 'Confirmed',
            PurchaseOrder::SHIPPED => 'Shipped',
            //PurchaseOrder::CUSTOMS => 'Customs',
            //PurchaseOrder::CLEARED => 'Cleared',
            PurchaseOrder::RECEIVED => 'Received',
            //PurchaseOrder::PAID => 'Paid'
        ];
    }

    public function is_overdue()
    {
        if (Carbon::now()->greaterThan(Carbon::parse($this->due_at))) {
            return true;
        }

        return false;
    }

    public function getOrderedDaysAgoAttribute()
    {
        return Carbon::parse($this->ordered_at)->diffForHumans();
    }

    public function scopeOrdered($query)
    {
        return $query->where('status', '>=', PurchaseOrder::ORDERED);
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

    /* * Retrieve due orders
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDue($query)
    {
        return $query->whereBetween('status', [PurchaseOrder::ORDERED, PurchaseOrder::CLEARED]);
    }

    /* * Retrieve overdued orders
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->whereBetween('status', [PurchaseOrder::ORDERED, PurchaseOrder::CLEARED])->where('due_at', '<', Carbon::now());
    }
}
