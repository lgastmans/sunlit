<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    use HasFactory;

    protected $fillable = ['credit_note_id'];

    protected $with = ['product', 'product.tax'];

    /**
     * Get the order associated with the item.
     */
    public function credit_note()
    {
        return $this->belongsTo(CreditNote::class);
    }

    /**
     * Get the product associated with the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPriceAttribute()
    {
        $total = number_format(($this->price + ($this->price * $this->tax/100)) * $this->quantity,2,'.',',');
        return  $total;
    }

    public function getTotalPriceUnfmtAttribute()
    {
        $total = ($this->price + ($this->price * $this->tax/100)) * $this->quantity;
        return  $total;
    }

    /**
     * Returns the Taxable Value
     */
    public function getTaxableValueAttribute()
    {
        return $this->quantity * $this->price;
    }

    /**
     * Returns the Tax Amount IGST
     */
    public function getTaxAmountAttribute()
    {
        return ($this->quantity * $this->price) * ($this->tax/100);
    }
    
}
