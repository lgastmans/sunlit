<?php

namespace App\Http\Controllers;

use App\Models\SaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSaleOrderRequest;

class SaleOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list sale orders')){
            $status = SaleOrder::getStatusList();
            return view('sale_orders.index', ['status' => $status]);

        }
        return abort(403, trans('error.unauthorized'));
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
            if ($column_index==1)
                $order_column = "suppliers.company";
            else
                $order_column = $column_arr[$column_index]['data'];
            $order_dir = $order_arr[0]['dir'];
        }

        $order_column = 'order_number';

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = SaleOrder::count();        

        $query = SaleOrder::query();
        $query->join('dealers', 'dealers.id', '=', 'dealer_id');
        $query->join('users', 'users.id', '=', 'user_id');
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

        $orders = $query->get();

        $arr = array();
        foreach($orders as $order)
        {           
            $arr[] = array(
                "id" => $order->id,
                "order_number" => $order->order_number,
                "dealer" => $order->dealer->company,
                "ordered_at" => $order->display_ordered_at,
                "due_at" => $order->display_due_at,
                "delivered_at" => $order->display_delivered_at,
                "amount_inr" => trans('app.currency_symbol_inr')." ".$order->amount,
                "status" => $order->display_status,
                "user" => $order->user->display_name
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
        $user = Auth::user();
        if ($user->can('edit sale orders')){
            $order = new SaleOrder();
            return view('sale_orders.form', ['order' => $order]);
        }
        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreSaleOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSaleOrderRequest $request)
    {
        $validatedData = $request->validated();
        $order = SaleOrder::create($validatedData);
        if ($order) {
            return redirect(route('sale-orders.cart', $order->order_number)); 
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'sale order']));        
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $order_number
     * @return \Illuminate\Http\Response
     */
    public function cart($order_number)
    {
        $order = SaleOrder::with(['dealer','items', 'items.product', 'items.product.tax'])->where('order_number', '=', $order_number)->first();
        if ($order){
            if ($order->status == SaleOrder::DRAFT)
                return view('sale_orders.cart', ['order' => $order ]);

            return redirect(route('sale-orders.show', $order->order_number)); 
        }
        
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SaleOrder  $saleOrder
     * @return \Illuminate\Http\Response
     */
    public function show(SaleOrder $saleOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SaleOrder  $saleOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(SaleOrder $saleOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SaleOrder  $saleOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SaleOrder $saleOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SaleOrder  $saleOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(SaleOrder $saleOrder)
    {
        //
    }
}
