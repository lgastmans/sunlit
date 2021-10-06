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
            if ($column_index==2)
                $order_column = "dealers.company";
            if ($column_index==6)
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
        $totalRecords = SaleOrderItem::where('product_id','=', $filter_product_id)->count();


        $query = SaleOrderItem::with('sale_order')
                ->join('sale_orders', 'sale_orders.id', '=', 'sale_order_id')
                ->join('users', 'users.id', '=', 'sale_orders.user_id')
                ->join('warehouses', 'warehouses.id', '=', 'sale_orders.warehouse_id')
                ->join('dealers', 'dealers.id', '=', 'sale_orders.dealer_id')
                ->where('product_id', '=', $filter_product_id);

        if (!empty($column_arr[0]['search']['value'])){
            $query->where('sale_orders.order_number', 'like', $column_arr[0]['search']['value'].'%');
        }
        if (!empty($column_arr[1]['search']['value'])){
            $query->where('warehouses.name', 'like', $column_arr[1]['search']['value'].'%');
        }
        if (!empty($column_arr[2]['search']['value'])){
            $query->where('dealers.company', 'like', $column_arr[2]['search']['value'].'%');
        }
        if (!empty($column_arr[3]['search']['value'])){
            $query->where('sale_order_items.quantity_ordered', 'like', $column_arr[3]['search']['value'].'%');
        }
        if (!empty($column_arr[4]['search']['value'])){
            $query->where('sale_orders.status', '=', $column_arr[4]['search']['value']);
        }
        if (!empty($column_arr[5]['search']['value'])){
            $query->where('sale_orders.ordered_at', 'like', convertDateToMysql($column_arr[5]['search']['value']));
        }
        if (!empty($column_arr[6]['search']['value'])){
            $query->where('users.name', 'like', $column_arr[6]['search']['value'].'%');
        }
                

        $totalRecordswithFilter = $query->count();

        $query->orderBy($order_column, $order_dir);

        if ($length > 0)
            $query->skip($start)->take($length);
        
        $orders = $query->get();

        $arr = array();
        foreach($orders as $order)
        {           
            $arr[] = array(
                "id" => $order->id,
                "ordered_at" => $order->sale_order->display_ordered_at,
                "order_number" => $order->sale_order->order_number,
                "quantity_ordered" => $order->quantity_ordered,
                "status" => $order->sale_order->display_status,
                "warehouse" => $order->sale_order->warehouse->name,
                "dealer" => $order->sale_order->dealer->company,
                "user" => $order->sale_order->user->display_name
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
