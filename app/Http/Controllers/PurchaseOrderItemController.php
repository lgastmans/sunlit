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



    public function getListForDatatables(Request $request)
    {
        $draw = 1;
        if ($request->has('draw'))
            $draw = $request->get('draw');

        $start = 0;
        if ($request->has('start'))
            $start = $request->get("start");

        $length = 10;
        if ($request->has('length')) {
            $length = $request->get("length");
        }

        $order_column = 'order_number';
        $order_dir = 'ASC';
        $order_arr = array();
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            $order_column = $column_arr[$column_index]['data'];
            $order_dir = $order_arr[0]['dir'];
        }

        $order_column = 'order_number';

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        if (!$request->has('filter_product_id'))
            return $arr;
        $filter_product_id = $request->get('filter_product_id');

        // Total records
        $totalRecords = PurchaseOrderItems::where('product_id','=', $filter_product_id)->count();
        // $totalRecordswithFilter = PurchaseOrder::join('suppliers', 'suppliers.id', '=', 'supplier_id')
        //     ->where('order_number', 'like', '%'.$search.'%')
        //     ->orWhere('suppliers.company', 'like', $search.'%')
        //     ->count();
        

        $orders = PurchaseOrderItems::with('order')->where('product_id', '=', $filter_product_id)->get();
        // $query->join('suppliers', 'suppliers.id', '=', 'supplier_id');
        // $query->join('users', 'users.id', '=', 'user_id');
        // if (!empty($column_arr[0]['search']['value'])){
        //     $query->where('purchase_orders.order_number', 'like', $column_arr[0]['search']['value'].'%');
        // }
        // if (!empty($column_arr[1]['search']['value'])){
        //     $query->where('suppliers.company', 'like', $column_arr[1]['search']['value'].'%');
        // }
        // if (!empty($column_arr[2]['search']['value'])){
        //     $query->where('purchase_orders.ordered_at', 'like', convertDateToMysql($column_arr[2]['search']['value']));
        // }
        // if (!empty($column_arr[3]['search']['value'])){
        //     $query->where('purchase_orders.due_at', 'like', convertDateToMysql($column_arr[3]['search']['value']));
        // }
        // if (!empty($column_arr[4]['search']['value'])){
        //     $query->where('purchase_orders.received_at', 'like', convertDateToMysql($column_arr[4]['search']['value']));
        // }
        // if (!empty($column_arr[5]['search']['value'])){
        //     $query->where('purchase_orders.amount_inr', 'like', $column_arr[5]['search']['value'].'%');
        // }
        // if (!empty($column_arr[6]['search']['value']) && $column_arr[6]['search']['value'] != "all"){
        //     $query->where('purchase_orders.status', 'like', $column_arr[6]['search']['value']);
        // }
        // if (!empty($column_arr[7]['search']['value'])){
        //     $query->where('users.name', 'like', $column_arr[7]['search']['value'].'%');
        // }
        
        // if ($request->has('search')){
        //     $search = $request->get('search')['value'];
        //     $query->where( function ($q) use ($search){
        //         $q->where('purchase_orders.order_number', 'like', $search.'%')
        //             ->orWhere('purchase_orders.amount_inr', 'like', $search.'%')
        //             ->orWhere('suppliers.company', 'like', $search.'%');
        //     });    
        // }

        $totalRecordswithFilter = $query->count();


        if ($length > 0)
            $query->skip($start)->take($length);
        
        // $orders = $query->get();

        $arr = array();
        foreach($orders as $order)
        {           
            $arr[] = array(
                "id" => $order->id,
                "order_number" => $order->order->order_number,
                "ordered_at" => $order->order->display_ordered_at,
                "quantity_ordered" => $order->order->quantity_ordered,
                "status" => $order->order->display_status,
                "warehouse" => $order->order->warehouse->name,
                "user" => $order->order->user->display_name
            );
        }

        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $arr,
            'error' => null
        );
        return response()->json($response);
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
            $item = PurchaseOrderItem::where('purchase_order_id', '=', $request->purchase_order_id)->where('product_id', '=', $request->product_id)->first();
            if ($item){
                $item->quantity_ordered = $request->quantity_ordered;
                $item->update();
            }
            else{
                $item = new PurchaseOrderItem();
                $item->purchase_order_id = $request->purchase_order_id;
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
        $item = PurchaseOrderItem::find($id);
        $order = PurchaseOrder::find($item->purchase_order_id);
        PurchaseOrderItem::destroy($id);

        return redirect(route('purchase-orders.cart', $order->order_number))->with('success', trans('app.record_deleted', ['field' => 'item']));

    }
}
