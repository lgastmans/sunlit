<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Dealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['state_id', 'company', 'address', 'address2', 'city', 'zip_code', 'gstin', 'contact_person', 'phone', 'phone2', 'email'];
    protected $with = ['state'];

    /**
     * Get the state associated with the dealer.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

}
