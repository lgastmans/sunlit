<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberFormatter;

class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['dealer_id', 'warehouse_id', 'quotation_number', 'quotation_number_slug', 'status', 'user_id', 'transport_charges '];

    protected $casts = [
        'pending_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    protected $with = ['dealer', 'warehouse', 'user', 'items'];

    const DRAFT = 1;

    const PENDING = 2;

    const CONFIRMED = 3;

    /**
     * calculated fields for Quotation
     */
    public $sub_total = 0;

    public $tax_total = 0;

    public $freight_charges = 0;   // = total weight * rate per kg / zone

    public $transport_total = 0;   // = freight_charges + with tax

    public $transport_tax_amount = 0;

    public $transport_tax_amount_unfmt = 0;

    public $tax_total_half = 0;

    public $total = 0;

    public $total_unfmt = 0;       // unformatted total

    public $total_spellout = '';

    public $total_without_transport = 0;

    public static function getStatusList()
    {
        return [
            Quotation::DRAFT => 'Draft',
            Quotation::PENDING => 'Pending',
            Quotation::CONFIRMED => 'Confirmed',
        ];
    }

    /**
     * Calculate the totals per Quotation
     */
    public function calculateTotals()
    {
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        if (is_null($this->transport_charges)) {
            $this->transport_charges = 0;
        }

        $this->sub_total = 0;
        $this->tax_total = 0;
        $this->freight_charges = (float) $this->transport_charges;
        $this->transport_total = 0;
        $this->total = 0;
        $this->total_unfmt = 0;

        /**
         * calculate the totals
         */
        $tax = 0;
        foreach ($this->items as $item) {
            $this->sub_total += $item->quantity * $item->price;
            $this->tax_total += ($item->quantity * $item->price) * ($item->tax / 100);

            /*
                select the highest tax from the list of items
            */
            if ($item->tax > $tax) {
                $tax = $item->tax;
            }
        }

        /**
         * add the Transport Charges to the totals
         */
        $this->sub_total += (float) $this->transport_charges;
        $this->transport_tax_amount = (float) ($this->transport_charges * $tax / 100);
        $this->transport_total = (float) $this->transport_charges + (float) $this->transport_tax_amount;
        $this->tax_total += (float) $this->transport_charges * $tax / 100;

        $this->total = $this->sub_total + $this->tax_total;

        $this->total = round($this->total);
        $this->total_without_transport = $fmt->formatCurrency($this->total - $this->transport_total, 'INR');

        /**
         * for SGST and CGST
         */
        $this->tax_total_half = $this->tax_total / 2;

        $this->total_spellout = $this->expandAmount($this->total);

        $this->sub_total = $fmt->formatCurrency($this->sub_total, 'INR');
        $this->tax_total = $fmt->formatCurrency($this->tax_total, 'INR');
        $this->transport_total = $fmt->formatCurrency($this->transport_total, 'INR');
        $this->transport_tax_amount_unfmt = (float) $this->transport_tax_amount;
        $this->transport_tax_amount = $fmt->formatCurrency((float) $this->transport_tax_amount, 'INR');
        $this->tax_total_half = $fmt->formatCurrency($this->tax_total_half, 'INR');
        $this->total_unfmt = $this->total;
        $this->total = $fmt->formatCurrency($this->total, 'INR');

        return true;
    }

    public static function expandAmount($amount)
    {

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::SPELLOUT);

        if (strpos($amount, '.') !== false) {

            $numwords = explode('.', $amount);

            if (intval($numwords[1]) > 0) {
                $res = $fmt->format($numwords[0]).' and paise '.$fmt->format($numwords[1]).' only';
            } else {
                $res = $fmt->format((int) $numwords[0]).' only';
            }
        } else {
            $res = $fmt->format($amount).' only';
        }
        $res = 'INR '.$res;

        return ucfirst($res);
    }

    /**
     * Get the items associated with the sale order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItems::class);
    }

    /**
     * Get the dealer associated with the sale order.
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * Get the warehouse associated with the sale order.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the user associated with the sale order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns the created_at date for display Month Day, Year
     */
    public function getDisplayCreatedAtAttribute()
    {
        if ($this->created_at) {
            $dt = Carbon::parse($this->created_at);

            return $dt->toFormattedDateString();
        }

        return '';
    }

    /**
     * Returns the pending_at date for display Month Day, Year
     */
    public function getDisplayPendingAtAttribute()
    {
        if ($this->pending_at) {
            $dt = Carbon::parse($this->pending_at);

            return $dt->toFormattedDateString();
        }

        return '';
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

    public function getDisplayStatusAttribute()
    {
        switch ($this->status) {
            case Quotation::DRAFT:
                $status = '<span class="badge badge-secondary-lighten">Draft</span>';
                break;
            case Quotation::PENDING:
                $status = '<span class="badge badge-info-lighten">Pending</span>';
                break;
            case Quotation::CONFIRMED:
                $status = '<span class="badge badge-primary-lighten">Confirmed</span>';
                break;
            default:
                $status = '<span class="badge badge-error-lighten">Unknown</span>';
        }

        return $status;
    }
}
