<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberFormatter;

class CreditNote extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['dealer_id', 'warehouse_id', 'credit_note_number', 'credit_note_number_slug', 'status', 'remarks', 'user_id', 'invoice_number', 'invoice_date', 'is_against_invoice'];

    protected $with = ['dealer', 'warehouse', 'user', 'items'];

    const DRAFT = 1;

    const CONFIRMED = 2;

    /**
     * calculated fields for Quotation
     */
    public $sub_total = 0;

    public $tax_total = 0;

    public $tax_total_half = 0;

    public $total = 0;

    public $total_unfmt = 0;       // unformatted total

    public $total_spellout = '';

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'invoice_date' => 'datetime',
        ];
    }

    public static function getStatusList()
    {
        return [
            CreditNote::DRAFT => 'Draft',
            CreditNote::CONFIRMED => 'Confirmed',
        ];
    }

    /**
     * Calculate the totals per Quotation
     */
    public function calculateTotals()
    {
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        $this->sub_total = 0;
        $this->tax_total = 0;
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

        $this->total = $this->sub_total + $this->tax_total;

        $this->total = round($this->total);

        /**
         * for SGST and CGST
         */
        $this->tax_total_half = $this->tax_total / 2;

        $this->total_spellout = $this->expandAmount($this->total);

        $this->sub_total = $fmt->formatCurrency($this->sub_total, 'INR');
        $this->tax_total = $fmt->formatCurrency($this->tax_total, 'INR');
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
        return $this->hasMany(CreditNoteItem::class);
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

    /**
     * Returns the invoice_date for display Month Day, Year
     */
    public function getDisplayInvoiceDateAttribute()
    {
        if ($this->invoice_date) {
            $dt = Carbon::parse($this->invoice_date);

            return $dt->toFormattedDateString();
        }

        return '';
    }

    public function getDisplayStatusAttribute()
    {
        switch ($this->status) {
            case CreditNote::DRAFT:
                $status = '<span class="badge badge-secondary-lighten">Draft</span>';
                break;
            case CreditNote::CONFIRMED:
                $status = '<span class="badge badge-primary-lighten">Confirmed</span>';
                break;
            default:
                $status = '<span class="badge badge-error-lighten">Unknown</span>';
        }

        return $status;
    }
}
