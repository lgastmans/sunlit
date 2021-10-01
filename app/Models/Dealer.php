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

    public static function getDealerFilterList()
    {
        $dealers = Dealer::select('id', 'company as name')
           ->orderBy('company')
           ->get();
        $arr = ['0'=> 'All'];

        foreach ($dealers as $dealer)
            $arr[$dealer->id] = $dealer->name;

        return $arr;
    }  
}
