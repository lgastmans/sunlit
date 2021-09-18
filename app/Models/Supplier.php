<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = ['state_id', 'company', 'address', 'address2', 'city', 'zip_code', 'gstin', 'contact_person', 'phone', 'phone2', 'email', 'currency'];
    protected $with = ['state'];

    /**
     * Get the state associated with the supplier.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }


    /**
     * Get the products associated with the supplier.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function purchase_orders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }


    public function getCurrencyCodeAttribute()
    {
        if ($this->currency == "inr")
            return trans('app.currency_symbol_inr');
        return trans('app.currency_symbol_usd');
    }


    // this is the recommended way for declaring event handlers
    public static function boot() 
    {
        parent::boot();

        self::deleting(function($supplier) { // before delete() method call this

             $supplier->products()->each(function($product) {
                $product->delete(); // <-- direct deletion
             });

        });
    }    
}
