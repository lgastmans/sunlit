<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Dealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['state_id', 'company', 'address', 'address2', 'city', 'zip_code', 'gstin', 'contact_person', 'phone', 'phone2', 'email', 'email2', 'email3', 'has_shipping_address', 'shipping_state_id', 'shipping_company', 'shipping_address', 'shipping_address2', 'shipping_city', 'shipping_zip_code', 'shipping_gstin', 'shipping_contact_person', 'shipping_phone', 'shipping_phone2', 'shipping_email'];
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

}
