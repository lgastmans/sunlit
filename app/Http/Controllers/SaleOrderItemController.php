<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SaleOrder;
use Illuminate\Http\Request;
use App\Models\SaleOrderItem;

class SaleOrderItemController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::find($request->get('product_id'));
        if ($product){
            $item = SaleOrderItem::where('sale_order_id', '=', $request->sale_order_id)->where('product_id', '=', $request->product_id)->first();
            if ($item){
                $item->quantity_ordered = $request->quantity_ordered;
                $item->update();
            }
            else{
                $item = new SaleOrderItem();
                $item->sale_order_id = $request->sale_order_id;
                $item->product_id = $request->product_id;
                $item->tax = $product->tax->amount;
                $item->quantity_ordered = $request->quantity_ordered;
                $item->selling_price = $request->selling_price;
                $item->save();
            }
            return response()->json(['success'=>'true','code'=>200, 'message'=> 'OK', 'item' => $item, 'product' => $product]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SaleOrderItem  $saleOrderItem
     * @return \Illuminate\Http\Response
     */
    public function show(SaleOrderItem $saleOrderItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SaleOrderItem  $saleOrderItem
     * @return \Illuminate\Http\Response
     */
    public function edit(SaleOrderItem $saleOrderItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = SaleOrderItem::find($id);
        if ($request->field == "quantity")
            $item->quantity_ordered = $request->value;

        if ($request->field == "price")
            $item->selling_price = $request->value;

        $item->update();
        return response()->json(['success'=>'true', 'code'=>200, 'message'=>'OK']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = SaleOrderItem::find($id);
        SaleOrderItem::destroy($id);

        $order = SaleOrder::find($item->sale_order_id);
        $order->amount = 0;
        foreach($order->items as $item){
            $order->amount += $item->total_price;
        }
        $order->update();
        return redirect(route('sale-orders.cart', $order->order_number))->with('success', trans('app.record_deleted', ['field' => 'item']));

    }
}
