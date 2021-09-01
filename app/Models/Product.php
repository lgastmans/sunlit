<?php

namespace App\Models;

use \NumberFormatter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates= ['created_at'];
    protected $fillable = ['category_id', 'supplier_id', 'tax_id', 'code', 'name', 'model', 'minimum_quantity', 'purchase_price', 'cable_length', 'kw_rating', 'part_number', 'notes'];


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

    /**
     * Get the tax associated with the product.
     */
    public function purchase_order_item()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }



    /**
     * Get the purchase price amount in decimal.
     *
     * @return string
     */
    public function getDisplayPurchasePriceAttribute()
    {
        $fmt = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
        
        return $fmt->format($this->purchase_price/100); //sprintf('%01.2f', $this->purchase_price / 100);
    }

}
