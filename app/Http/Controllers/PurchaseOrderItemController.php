<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;


class PurchaseOrderItemController extends Controller
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
            $item = new PurchaseOrderItem;
            $item->purchase_order_id = $request->purchase_order_id;
            $item->product_id = $request->product_id;
            $item->tax_id = $product->tax->id;
            $item->quantity_ordered = $request->quantity_ordered;
            $item->selling_price = $request->selling_price*100;
            $item->save();
            return response()->json(['success'=>'true','code'=>200, 'message'=> 'OK', 'item' => $item, 'product' => $product]);
        }
        
            
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $item = PurchaseOrderItem::find($id);
        if ($request->field == "quantity")
            $item->quantity_ordered = $request->value;

        if ($request->field == "price")
            $item->selling_price = $request->value*100;

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
        $item = PurchaseOrderItem::find($id);
        $order = PurchaseOrder::find($item->purchase_order_id);
        PurchaseOrderItem::destroy($id);

        return redirect(route('purchase-orders.cart', $order->order_number))->with('success', trans('app.record_deleted', ['field' => 'item']));

    }
}
