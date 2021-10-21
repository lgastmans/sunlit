<?php

namespace App\Models;

use Carbon\Carbon;
use \NumberFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates= ['created_at'];
    protected $fillable = ['category_id', 'supplier_id', 'tax_id', 'code', 'name', 'model', 'minimum_quantity', 'purchase_price', 'kw_rating', 'part_number', 'notes', 'cable_length_input', 'cable_length_output', 'weight_actual', 'weight_volume', 'weight_calculated', 'warranty'];
    protected $with = ['tax', 'inventory'];

    /**
     * Get the category associated with the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier associated with the product.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the tax associated with the product.
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function purchase_order_item()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }


    public function sale_order_item()
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function movement()
    {
        return $this->hasMany(InventoryMovement::class);
    }



    /**
     * Get the purchase price amount in decimal.
     *
     * @return string
     */
    public function getDisplayPurchasePriceAttribute()
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

}
