<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;


class Tax extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'amount'];

    /**
     * Get the tax amount in decimal.
     *
     * @return string
     */
    public function getDisplayAmountAttribute()
    {
       return sprintf('%01.2f', $this->amount / 100);
    }

    /**
     * Set the tax amount as integer.
     *
     * @param  string  $value
     * @return void
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }


    /**
     * Get the products associated with the tax.
     */
    public function products()
    {
        return $this->HasMany(Product::class);
    }

}
