<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItems;
use Illuminate\Http\Request;

class QuotationItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::find($request->get('product_id'));

        if ($product) {

            $item = QuotationItems::where('quotation_id', '=', $request->quotation_id)->where('product_id', '=', $request->product_id)->first();

            $order = Quotation::find($request->quotation_id);

            if ($item) {
                $item->quantity = $request->quantity;
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

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(QuotationItems $quotationItems)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(QuotationItems $quotationItems)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\QuotationItems  $quotationItems
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = QuotationItems::find($id);

        $order = Quotation::find($item->quotation_id);

        $update_quantity = 0;

        if ($request->field == 'quantity') {

            if ($order->status == Quotation::DRAFT) {
                $item->quantity = $request->value;
            } else {
                $update_quantity = $request->value - $item->quantity;

                $item->quantity = $request->value;
            }
        }

        if ($request->field == 'price') {
            $item->price = $request->value;
        }

        $item->update();

        return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QuotationItems  $quotationItems
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = QuotationItems::find($id);

        $order = Quotation::find($item->quotation_id);

        QuotationItems::destroy($id);

        $order = Quotation::find($item->quotation_id);
        $order->amount = 0;
        foreach ($order->items as $item) {
            $order->amount += $item->price;
        }
        $order->update();

        return redirect(route('quotations.show', $order->quotation_number_slug))->with('success', trans('app.record_deleted', ['field' => 'item']));
    }
}
