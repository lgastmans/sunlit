<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['state_id', 'name', 'address', 'city', 'zip_code', 'contact_person', 'phone', 'phone2', 'email'];

    protected $with = ['state'];

    /**
     * Get the state associated with the warehouse.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function purchase_orders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function sale_orders(): HasMany
    {
        return $this->hasMany(SaleOrder::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
