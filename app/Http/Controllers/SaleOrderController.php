<?php

namespace App\Http\Controllers;

use App\Models\SaleOrder;
use Illuminate\Http\Request;
use App\Models\SaleOrderItem;
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

        $query->orderBy($order_column, $order_dir);
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
                "amount" => (isset($order->amount)) ? trans('app.currency_symbol_inr')." ".$order->amount : "",
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
        $order = SaleOrder::where('order_number', '=', $order_number)->first();
        if ($order){
            if ($order->status == SaleOrder::DRAFT)
                return view('sale_orders.cart', ['order' => $order ]);

            return redirect(route('sale-orders.show', $order->order_number)); 
        }
        
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $order_number
     * @return \Illuminate\Http\Response
     */
    public function show($order_number)
    {
        $user = Auth::user();
        if ($user->can('view sale orders')){
            $order = SaleOrder::where('order_number', '=', $order_number)->first();
            if ($order)
                return view('sale_orders.show', ['order' => $order ]);

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'sale order']));
        }
        return abort(403, trans('error.unauthorized'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->get('field') == "order_number"){
            $hasOrderNumber = SaleOrder::where('order_number', 'LIKE', $request->get('order_number'))->count();
            if ($hasOrderNumber){
                return response()->json([
                    'success' => 'false',
                    'errors'  => "This order number is already used by another order",
                ], 409);
            }
            $order = SaleOrder::find($id);
            $order->order_number = $request->get('order_number');
            $order->update();
            return response()->json(['success'=>'true','code'=>200, 'message'=> 'OK', 'field' => $request->get('field')]);
        }

        if ($request->get('field') == "amount"){
            $order = SaleOrder::find($id);
            $items = SaleOrderItem::where('sale_order_id', "=", $id)->get();
            $order->amount = 0;
            foreach($items as $item){
                $order->amount += $item->total_price;
            }
            $order->update();

            return response()->json(['success'=>'true','code'=>200, 'message'=> 'OK', 'field' => $request->get('field')]);
        }
    }


        /**
     * Update the ordered_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ordered(Request $request, $id)
    {
        $validated = $request->validate([
            'ordered_at' => 'required|date'
        ]);

        $order = SaleOrder::find($id);
        $order->ordered_at = $request->get('ordered_at');
        $order->status = SaleOrder::ORDERED;
        $items = SaleOrderItem::where('sale_order_id', "=", $id)->select('quantity_ordered', 'selling_price', 'tax')->get();
        $order->amount = 0;
        foreach($items as $item){
            $order->amount += $item->total_price; 
        }
    
        $order->update();
        return redirect(route('sale-orders.show', $order->order_number))->with('success', 'order placed'); 
    }


     /**
     * Update the confirmed_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirmed(Request $request, $id)
    {
        $validated = $request->validate([
            'confirmed_at' => 'required|date'
        ]);
        $order = SaleOrder::with('items')->find($id);
        $order->confirmed_at = $request->get('confirmed_at');
        $order->status = SaleOrder::CONFIRMED;
        $order->update();

        // $inventory = new Inventory();
        // $inventory->updateStock($order);
        
        return redirect(route('sale-orders.show', $order->order_number))->with('success', 'order confirmed'); 
    }


    /**
     * Update the shipped_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function shipped(Request $request, $id)
    {
        $validated = $request->validate([
            'shipped_at' => 'required|date',
            'due_at' => 'required|date',
            'tracking_number' => 'required',
            'courier' => 'required'
        ]);
        $order = SaleOrder::find($id);
        $order->shipped_at = $request->get('shipped_at');
        $order->due_at = $request->get('due_at');
        $order->tracking_number = $request->get('tracking_number');
        $order->courier = $request->get('courier');
        $order->status = SaleOrder::SHIPPED;
        $order->update();
        return redirect(route('sale-orders.show', $order->order_number))->with('success', 'order shipped'); 
    }


    /**
     * Update the delivered_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delivered(Request $request, $id)
    {
        $order = SaleOrder::find($id);
        $order->delivered_at = $request->get('delivered_at');
        $order->status = SaleOrder::DELIVERED;
        $order->update();

        // $inventory = new Inventory();
        // $inventory->updateStock($order);

        return redirect(route('sale-orders.show', $order->order_number))->with('success', 'order delivered'); 
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->can('delete sale orders')){
            $order = SaleOrder::find($id);
            $order->items()->delete();
            $order->delete();
            return redirect(route('sale-orders'))->with('success', trans('app.record_deleted', ['field' => 'Sale Order']));
        }
        return abort(403, trans('error.unauthorized'));

    }
}
