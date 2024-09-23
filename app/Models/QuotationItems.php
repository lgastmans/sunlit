<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItems extends Model
{
    use HasFactory;

    protected $fillable = ['quotation_id'];

    protected $with = ['product', 'product.tax'];

    /**
     * Get the order associated with the item.
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Get the product associated with the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::find($request->get('product_id'));

        if ($product) {

            $item = QuotationItems::where('quotation_id', '=', $request->quotation_id)->where('product_id', '=', $request->product_id)->first();

            $order = Quotation::find($request->quotation_id);

            if ($item) {
                $item->quantity_ordered = $request->quantity_ordered;
                $item->update();
            } else {
                $item = new QuotationItems;
                $item->quotation_id = $request->quotation_id;
                $item->product_id = $request->product_id;
                $item->tax = $product->tax->amount;
                $item->quantity = $request->quantity;
                $item->price = $request->price;
                $item->save();
            }

            return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'item' => $item, 'product' => $product]);
        }
    }

    public function getTotalPriceAttribute()
    {
        $total = number_format(($this->price + ($this->price * $this->tax / 100)) * $this->quantity, 2, '.', ',');

        return $total;
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
        return ($this->quantity * $this->price) * ($this->tax / 100);
    }
}
