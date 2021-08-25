<?php

namespace App\Models;

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
     * Get the purchase price amount in decimal.
     *
     * @return string
     */
    public function getDisplayPurchasePriceAttribute()
    {
       return sprintf('%01.2f', $this->purchase_price / 100);
    }

    /**
     * Set the purchase price amount as integer.
     *
     * @param  string  $value
     * @return void
     */
    public function setPurchasePriceAttribute($value)
    {
        $this->attributes['purchase_price'] = $value * 100;
    }    
}
