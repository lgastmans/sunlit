<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberFormatter;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['category_id', 'supplier_id', 'tax_id', 'code', 'name', 'model', 'minimum_quantity', 'purchase_price', 'kw_rating', 'part_number', 'notes', 'cable_length_input', 'cable_length_output', 'weight_actual', 'weight_volume', 'weight_calculated', 'warranty'];

    protected $with = ['tax', 'inventory'];

    /**
     * Get the category associated with the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier associated with the product.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the tax associated with the product.
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function purchase_order_item(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function sale_order_item(): HasMany
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function movement(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get the purchase price amount in decimal.
     */
    public function getDisplayPurchasePriceAttribute(): string
    {
        return $this->purchase_price;
        // $fmt = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);

        // return $fmt->format($this->purchase_price/100); //sprintf('%01.2f', $this->purchase_price / 100);
    }

    public function getDisplayCreatedAtAttribute()
    {
        $dt = Carbon::parse($this->created_at);

        return $dt->toFormattedDateString();
    }

    /**
     * Get the last purchased date for a product
     *
     * @param int
     */
    public function getLastPurchasedOnAttribute()
    {
        $result = InventoryMovement::select('created_at')
            ->whereNotNull('purchase_order_id')
            ->where('product_id', '=', $this->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($result) {
            $dt = Carbon::parse($result->created_at);

            return $dt->toFormattedDateString();
        }

        return 'NF'; // Not Found
    }

    /**
     * Get the last sold date for a product
     *
     * @param int
     */
    public function getLastSoldOnAttribute()
    {
        $result = InventoryMovement::select('created_at')
            ->whereNotNull('sales_order_id')
            ->where('product_id', '=', $this->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($result) {
            $dt = Carbon::parse($result->created_at);

            return $dt->toFormattedDateString();
        }

        return 'NF'; // Not Found
    }
}
