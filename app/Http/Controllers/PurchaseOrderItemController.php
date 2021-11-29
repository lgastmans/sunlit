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

            if ($column_index==1)
                $order_column = "warehouses.name";
            // if ($column_index==2)
            //     $order_column = "dealers.company";
            if ($column_index==5)
                $order_column = "users.name";

            $order_dir = $order_arr[0]['dir'];
        }

    
        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        $arr = array();
        if (!$request->has('filter_product_id'))
            return $arr;
        $filter_product_id = $request->get('filter_product_id');

        // Total records
        $totalRecords = PurchaseOrderItem::where('product_id','=', $filter_product_id)->count();


        $query = PurchaseOrderItem::with('purchase_order')
            ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_id')
            ->join('users', 'users.id', '=', 'purchase_orders.user_id')
            ->join('warehouses', 'warehouses.id', '=', 'purchase_orders.warehouse_id')
            ->where('product_id', '=', $filter_product_id);


        if (!empty($column_arr[0]['search']['value'])){
            $query->where('purchase_orders.order_number', 'like', $column_arr[0]['search']['value'].'%');
        }
        if (!empty($column_arr[1]['search']['value'])){
            $query->where('warehouses.name', 'like', $column_arr[1]['search']['value'].'%');
        }
        if (!empty($column_arr[2]['search']['value'])){
            $query->where('purchase_orders.quantity_confirmed', '=', $column_arr[2]['search']['value']);
        }
        if (!empty($column_arr[3]['search']['value'])){
            $query->where('purchase_orders.status', '=', $column_arr[3]['search']['value']);
        }
        if (!empty($column_arr[4]['search']['value'])){
            $query->where('purchase_orders.ordered_at', 'like', convertDateToMysql($column_arr[4]['search']['value']));
        }
        if (!empty($column_arr[5]['search']['value'])){
            $query->where('users.name', 'like', $column_arr[5]['search']['value'].'%');
        }


        $totalRecordswithFilter = $query->count();

        $query->orderBy($order_column, $order_dir);

        if ($length > 0)
            $query->skip($start)->take($length);
        
        $orders = $query->get();

        $arr = array();
        foreach($orders as $record)
        {           
            $arr[] = array(
                "id" => $record->id,
                "ordered_at" => $record->purchase_order->display_ordered_at,
                "order_number" => $record->purchase_order->order_number,
                "quantity_confirmed" => $record->quantity_confirmed,
                "status" => $record->purchase_order->display_status,
                "warehouse" => $record->purchase_order->warehouse->name,
                "user" => $record->purchase_order->user->display_name
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
                $item->quantity_confirmed = $request->quantity_confirmed;
                $item->update();
            }
            else{
                $item = new PurchaseOrderItem();
                $item->purchase_order_id = $request->purchase_order_id;
                $item->product_id = $request->product_id;
                $item->tax = $product->tax->amount;
                $item->quantity_confirmed = $request->quantity_confirmed;
                $item->buying_price = $request->buying_price;
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
            $item->quantity_confirmed = $request->value;

        if ($request->field == "price")
            $item->quantity_confirmed = $request->value;

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
        PurchaseOrderItem::destroy($id);

        $order = PurchaseOrder::with('items')->find($item->purchase_order_id);
        $order->amount_usd = 0;
        foreach($order->items as $item){
            $order->amount_usd += $item->total_price; 
        }
        $order->amount_inr = $order->amount_usd * $order->order_exchange_rate;
        $order->update();

        return redirect(route('purchase-orders.cart', $order->order_number))->with('success', trans('app.record_deleted', ['field' => 'item']));

    }
}
