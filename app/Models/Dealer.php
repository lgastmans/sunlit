<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['state_id', 'company', 'address', 'address2', 'city', 'zip_code', 'gstin', 'contact_person', 'phone', 'phone2', 'email', 'email2', 'email3', 'has_shipping_address', 'shipping_state_id', 'shipping_company', 'shipping_address', 'shipping_address2', 'shipping_address3', 'shipping_city', 'shipping_zip_code', 'shipping_gstin', 'shipping_contact_person', 'shipping_contact_person2', 'shipping_phone', 'shipping_phone2', 'shipping_email', 'shipping_email2'];

    protected $with = ['state'];

    /**
     * Get the state associated with the dealer.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the shipping state associated with the dealer.
     */
    public function shipping_state()
    {
        return $this->belongsTo(State::class);
    }

    public function sale_orders()
    {
        return $this->hasMany(SaleOrder::class);
    }
}
