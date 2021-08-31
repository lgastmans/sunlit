<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $purchase_order_item = [
            'purchase_order_id'=>mt_rand(1, 10), 
            'purchase_order_item_id' => mt_rand(1, 10), 
            'product_id' => mt_rand(1, 10), 
            'code'=>'XTM 4000-48', 
            'name'=>'XTM 4000-48', 
            'model'=>'Xtender series', 
            'quantity'=>mt_rand(1, 100), 
            'price'=>mt_rand(1, 100).mt_rand(1, 100)];
            
        $res = ['code' => 200, 'message' => 'success', 'item'=> $purchase_order_item];
        return json_encode($res);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
