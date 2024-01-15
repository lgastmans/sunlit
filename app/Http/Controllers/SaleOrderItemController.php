<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use NumberFormatter;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\SaleOrder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SaleOrderItem;
use Illuminate\Support\Facades\DB;

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
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

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

            if ($column_index==0)
                $order_column = "sale_orders.order_number";
            if ($column_index==1)
                $order_column = "warehouses.name";
            if ($column_index==2)
                $order_column = "dealers.company";
            if ($column_index==3)
                $order_column = "sale_order_items.quantity_ordered";
            //if ($column_index==4)
            if ($column_index==5)
                $order_column = "sale_orders.status";
            if ($column_index==6)
                $order_column = "sale_orders.booked_at";
            if ($column_index==7)
                $order_column = "sale_orders.dispatched_at";
            if ($column_index==8)
                $order_column = "sale_orders.created_at";
            if ($column_index==9)
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
                ->select('sale_order_items.id', 'sale_orders.created_at', 'sale_orders.booked_at', 'sale_orders.dispatched_at', 'sale_orders.order_number',
                    'sale_orders.order_number_slug', 'sale_order_items.quantity_ordered', 'sale_order_items.selling_price', 'sale_orders.status', 
                    'warehouses.name AS warehouse_name', 'dealers.company AS dealer_company', 'users.name AS user_name')
                ->join('sale_orders', 'sale_orders.id', '=', 'sale_order_id')
                ->join('users', 'users.id', '=', 'sale_orders.user_id')
                ->join('warehouses', 'warehouses.id', '=', 'sale_orders.warehouse_id')
                ->join('dealers', 'dealers.id', '=', 'sale_orders.dealer_id')
                ->where('product_id', '=', $filter_product_id);

        if (!empty($column_arr[0]['search']['value'])){
            $query->where('sale_orders.order_number', 'like', '%'.$column_arr[0]['search']['value'].'%');
        }
        if (!empty($column_arr[1]['search']['value'])){
            $query->where('warehouses.name', 'like', '%'.$column_arr[1]['search']['value'].'%');
        }
        if (!empty($column_arr[2]['search']['value'])){
            $query->where('dealers.company', 'like', '%'.$column_arr[2]['search']['value'].'%');
        }
        if (!empty($column_arr[3]['search']['value'])){
            $query->where('sale_order_items.quantity_ordered', 'like', $column_arr[3]['search']['value'].'%');
        }
        if (!empty($column_arr[5]['search']['value'])){
            $query->where('sale_orders.status', '=', $column_arr[5]['search']['value']);
        }
        if (!empty($column_arr[6]['search']['value'])){
            $query->where('sale_orders.booked_at', 'like', convertDateToMysql($column_arr[6]['search']['value']));
        }
        if (!empty($column_arr[7]['search']['value'])){
            $query->where('sale_orders.dispatched_at', 'like', convertDateToMysql($column_arr[7]['search']['value']));
        }
        if (!empty($column_arr[8]['search']['value'])){
            $query->where('sale_orders.created_at', 'like', convertDateToMysql($column_arr[8]['search']['value']));
        }
        if (!empty($column_arr[9]['search']['value'])){
            $query->where('users.name', 'like', $column_arr[9]['search']['value'].'%');
        }
                
        if ($request->has('month_id')){
            $month = $request->get('month_id');  
            $query->whereMonth('sale_orders.dispatched_at', '=', $month);
        }

        $totalRecordswithFilter = $query->count();

        $query->orderBy($order_column, $order_dir);

        if ($length > 0)
            $query->skip($start)->take($length);
//$orders = $query->toSql();
//dd($orders);
        $orders = $query->get();

        $arr = array();
        foreach($orders as $order)
        {

            if ($order->status==SaleOrder::DRAFT)
                $display_status = '<span class="badge badge-secondary-lighten">Draft</span>';
            elseif ($order->status==SaleOrder::BOOKED)
                $display_status = '<span class="badge badge-primary-lighten">Booked</span>';
            elseif ($order->status==SaleOrder::DISPATCHED)
                $display_status = '<span class="badge badge-dark-lighten">Dispatched</span>';
            else
                $display_status = '<span class="badge badge-error-lighten">Unknown</span>';


            $arr[] = array(
                "id" => $order->id,
                "created_at" => Carbon::parse($order->created_at->toDateString())->toFormattedDateString(),
                "booked_at" => Carbon::parse($order->booked_at)->toFormattedDateString(),
                "dispatched_at" => Carbon::parse($order->dispatched_at)->toFormattedDateString(),
                "order_number" => $order->order_number,
                "order_number_slug" => $order->order_number_slug,
                "quantity_ordered" => number_format($order->quantity_ordered,0,'.',','),
                "selling_price" => $fmt->formatCurrency($order->selling_price,"INR"),
                "status" => $display_status,
                "warehouse" => $order->warehouse_name,
                "dealer" => $order->dealer_company, //(isset($order->dealer) ? $order->company : 'not specified'),
                "user" => $order->user_name
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

            $order = SaleOrder::find($request->sale_order_id);

            $inventory = new Inventory();

            if ($item){
                $item->quantity_ordered = $request->quantity_ordered;
                $item->update();

                $inventory->updateItemStock($order, $item->product_id, ($request->quantity_ordered - $item->quantity_ordered));
            }
            else{
                $item = new SaleOrderItem();
                $item->sale_order_id = $request->sale_order_id;
                $item->product_id = $request->product_id;
                $item->tax = $product->tax->amount;
                $item->quantity_ordered = $request->quantity_ordered;
                $item->selling_price = $request->selling_price;
                $item->save();

                $inventory->updateItemStock($order, $item->product_id, $request->quantity_ordered);
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

        $order = SaleOrder::find($item->sale_order_id);

        $update_quantity = 0;

        if ($request->field == "quantity") {

            if ($order->status == SaleOrder::DRAFT) {
                $item->quantity_ordered = $request->value;
            }
            else {
                $update_quantity = $request->value - $item->quantity_ordered;

                $item->quantity_ordered = $request->value;
            
                $inventory = new Inventory();
                $inventory->updateItemStock($order, $item->product_id, $update_quantity);
            }
        }

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

        $order = SaleOrder::find($item->sale_order_id);

        $inventory = new Inventory();
        $inventory->updateItemStock($order, $item->product_id, ($item->quantity_ordered*-1));

        SaleOrderItem::destroy($id);

        $order = SaleOrder::find($item->sale_order_id);
        $order->amount = 0;
        foreach($order->items as $item){
            $order->amount += $item->total_price;
        }
        $order->update();
        return redirect(route('sale-orders.cart', $order->order_number_slug))->with('success', trans('app.record_deleted', ['field' => 'item']));

    }

    public static function getNumberAndTotalSaleByRange($range)
    {
        $query = DB::table('sale_order_items')
                ->join('products', 'products.id', '=', 'sale_order_items.product_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->select('categories.name as category', 'sale_order_items.created_at', DB::raw('count(DISTINCT (sale_order_items.sale_order_id)) as number_sale'), DB::raw('sum((sale_order_items.quantity_ordered * sale_order_items.selling_price)) as total_sale'));
          
        if ( $range == 'daily'){
            $query->addSelect(DB::raw("DATE_FORMAT(sale_order_items.created_at, '%Y-%m-%d') date_range"),  DB::raw('YEAR(sale_order_items.created_at) year, MONTH(sale_order_items.created_at) month, DAY(sale_order_items.created_at) day'))
                ->groupByRaw('products.category_id, year, month, day')
                ->OrderByRaw('year, month, day, products.category_id');
        }

        if ( $range == 'monthly'){
            $query->addSelect(DB::raw("DATE_FORMAT(sale_order_items.created_at, '%Y-%m') date_range"),  DB::raw('YEAR(sale_order_items.created_at) year, MONTH(sale_order_items.created_at) month'))
                ->groupByRaw('products.category_id, year, month')
                ->OrderByRaw('year, month,products.category_id');
        }
        if ( $range == 'yearly'){
            $query->addSelect(DB::raw("DATE_FORMAT(sale_order_items.created_at, '%Y') date_range"),  DB::raw('YEAR(sale_order_items.created_at) year'))
                ->groupByRaw('products.category_id, year')
                ->OrderByRaw('year, products.category_id');
        }
                    
        $sales = $query->get();
        $categories = $sales->pluck('category')->unique();
        $series = [];
        foreach($categories as $cat){
            $series[Str::lower(Str::replace(' ','-',$cat))]['name'] = $cat;
            $series[Str::lower(Str::replace(' ','-',$cat))]['data'] = [] ;
        }
        // dd($series);
        foreach($sales as $sale){
           $data_point['x']= \Carbon\Carbon::parse($sale->created_at)->timestamp;
           $data_point['y']= $sale->total_sale;
           array_push($series[Str::lower(Str::replace(' ','-',$sale->category))]['data'], $data_point);
        }
        // dd($series);
        return response()->json(['series' => $series]);
    }
}
