<?php

namespace App\Http\Controllers;

use \NumberFormatter;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use \App\Http\Requests\StorePurchaseOrderRequest;


class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list purchase orders')){
            $status = PurchaseOrder::getStatusList();
            return view('purchase_orders.index', ['status' => $status]);

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

            // the purchase order datatable isn't the same in index than in warehouse>purchase orders 
            if ($request->has('source') && $request->source == "warehouses"){
                switch ($column_index){
                    case 1:
                        $order_column = "suppliers.company";
                        break;
                    case 4:
                            $order_column = "users.name";
                            break;
                    default:
                        $order_column = $column_arr[$column_index]['data'];
                }
            }else{
                switch ($column_index){
                    case 1:
                        $order_column = "purchase_orders.warehouse_id";
                        break;
                    case 2:
                        $order_column = "suppliers.company";
                        break;
                    default:
                        $order_column = $column_arr[$column_index]['data'];
                }
            }
            $order_dir = $order_arr[0]['dir'];
        }


        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        $totalRecords = PurchaseOrder::count();
        

        $query = PurchaseOrder::query();
        $query->join('suppliers', 'suppliers.id', '=', 'supplier_id');
        $query->join('warehouses', 'warehouses.id', '=', 'warehouse_id');
        $query->join('users', 'users.id', '=', 'user_id');

        if ($request->has('source') && $request->source == "warehouses"){
            if (!empty($column_arr[0]['search']['value'])){
                $query->where('purchase_orders.order_number', 'like', $column_arr[0]['search']['value'].'%');
            }
            if (!empty($column_arr[1]['search']['value'])){
                $query->where('suppliers.company', 'like', $column_arr[1]['search']['value'].'%');
            }
            if (!empty($column_arr[2]['search']['value'])){
                $query->where('purchase_orders.status', 'like', $column_arr[2]['search']['value']);
            }
            if (!empty($column_arr[3]['search']['value'])){
                $query->where('purchase_orders.ordered_at', 'like', convertDateToMysql($column_arr[3]['search']['value']));
            }
            if (!empty($column_arr[4]['search']['value'])){
                $query->where('users.name', 'like', $column_arr[4]['search']['value'].'%');
            }
        }else{
            if (!empty($column_arr[0]['search']['value'])){
                $query->where('purchase_orders.order_number', 'like', $column_arr[0]['search']['value'].'%');
            }
            if (!empty($column_arr[1]['search']['value'])){
                $query->where('warehouses.name', 'like', $column_arr[1]['search']['value'].'%');
            }
            if (!empty($column_arr[2]['search']['value'])){
                $query->where('suppliers.company', 'like', $column_arr[2]['search']['value'].'%');
            }
            if (!empty($column_arr[3]['search']['value'])){
                $query->where('purchase_orders.ordered_at', 'like', convertDateToMysql($column_arr[3]['search']['value']));
            }
            if (!empty($column_arr[4]['search']['value'])){
                $query->where('purchase_orders.due_at', 'like', convertDateToMysql($column_arr[4]['search']['value']));
            }
        
            if (!empty($column_arr[5]['search']['value'])){
                $query->where('purchase_orders.amount_inr', 'like', $column_arr[5]['search']['value'].'%');
            }
            if (!empty($column_arr[6]['search']['value']) && $column_arr[6]['search']['value'] != "all"){
                $query->where('purchase_orders.status', 'like', $column_arr[6]['search']['value']);
            }
            if (!empty($column_arr[7]['search']['value'])){
                $query->where('users.name', 'like', $column_arr[7]['search']['value'].'%');
            }
        }
        
        
        if ($request->has('search')){
            $search = $request->get('search')['value'];
            $query->where( function ($q) use ($search){
                $q->where('purchase_orders.order_number', 'like', $search.'%')
                    ->orWhere('purchase_orders.amount_inr', 'like', $search.'%')
                    ->orWhere('suppliers.company', 'like', $search.'%');
            });    
        }

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
                "warehouse" => $order->warehouse->name,
                "supplier" => $order->supplier->company,
                "ordered_at" => $order->display_ordered_at,
                "expected_at" => $order->display_due_at,
                "amount_inr" => (isset($order->amount_inr)) ? trans('app.currency_symbol_inr')." ".$order->amount_inr : "",
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
        if ($user->can('edit purchase orders')){
            $order = new PurchaseOrder();
            return view('purchase_orders.form', ['purchase_order' => $order]);
        }
        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseOrderRequest $request)
    {
        $validatedData = $request->validated();
        $order = PurchaseOrder::create($validatedData);
        if ($order) {
            return redirect(route('purchase-orders.cart', $order->order_number)); 
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'purchase order']));        
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $order_number
     * @return \Illuminate\Http\Response
     */
    public function cart($order_number)
    {
        $order = PurchaseOrder::with(['supplier','warehouse','items', 'items.product', 'items.product.tax'])->where('order_number', '=', $order_number)->first();
        if ($order){
            if ($order->status == PurchaseOrder::DRAFT)
                return view('purchase_orders.cart', ['purchase_order' => $order ]);

            return redirect(route('purchase-orders.show', $order->order_number)); 
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
        if ($user->can('view purchase orders')){
            $purchase_order = PurchaseOrder::with(['supplier', 'warehouse', 'items', 'items.product'])->where('order_number', '=', $order_number)->first();
            if ($purchase_order)
                return view('purchase_orders.show', ['purchase_order' => $purchase_order ]);

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'purchase order']));
        }
        return abort(403, trans('error.unauthorized'));
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
        if ($request->get('field') == "order_number"){
            $hasOrderNumber = PurchaseOrder::where('order_number', 'LIKE', $request->get('order_number'))->count();
            if ($hasOrderNumber){
                return response()->json([
                    'success' => 'false',
                    'errors'  => "This order number is already used by another order",
                ], 409);
            }
            $order = PurchaseOrder::find($id);
            $order->order_number = $request->get('order_number');
            $order->update();
            return response()->json(['success'=>'true','code'=>200, 'message'=> 'OK', 'field' => $request->get('field')]);
        }

        if ($request->get('field') == "warehouse"){
            $order = PurchaseOrder::find($id);
            $order->warehouse_id = $request->get('warehouse_id');
            $order->update();
            $warehouse = Warehouse::find($request->get('warehouse_id'));
            return response()->json(['success'=>'true','code'=>200, 'message'=> 'OK', 'field' => $request->get('field'), 'warehouse'=> $warehouse]);
        }

        if ($request->get('field') == "amount"){
            $order = PurchaseOrder::find($id);
            $items = PurchaseOrderItem::where('purchase_order_id', "=", $id)->get();
            $order->amount_usd = 0;
            foreach($items as $item){
                $order->amount_usd += $item->total_price;
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

        $order = PurchaseOrder::find($id);
        $order->ordered_at = $request->get('ordered_at');
        $order->status = PurchaseOrder::ORDERED;
        $order->order_exchange_rate = \Setting::get('purchase_order.exchange_rate');
        $items = PurchaseOrderItem::where('purchase_order_id', "=", $id)->select('quantity_ordered', 'selling_price', 'tax')->get();
        $order->amount_usd = 0;
        foreach($items as $item){
            $order->amount_usd += $item->total_price; 
        }
        $order->amount_inr = $order->amount_usd * $order->order_exchange_rate;
        
        $order->update();
        return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order placed'); 
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
        $order = PurchaseOrder::with('items')->find($id);
        $order->confirmed_at = $request->get('confirmed_at');
        $order->status = PurchaseOrder::CONFIRMED;
        $order->update();

        $inventory = new Inventory();
        $inventory->updateStock($order);
        
        return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order confirmed'); 
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
        $order = PurchaseOrder::find($id);
        $order->shipped_at = $request->get('shipped_at');
        $order->due_at = $request->get('due_at');
        $order->tracking_number = $request->get('tracking_number');
        $order->courier = $request->get('courier');
        $order->status = PurchaseOrder::SHIPPED;
        $order->update();
        return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order shipped'); 
    }


    /**
     * Update the shipped_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function customs(Request $request, $id)
    {
        $validated = $request->validate([
            'customs_at' => 'required|date',
            'boe_number' => 'required',
        ]);
        $order = PurchaseOrder::find($id);
        $order->customs_at = $request->get('customs_at');
        $order->boe_number = $request->get('boe_number');
        $order->status = PurchaseOrder::CUSTOMS;
        $order->update();
        return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order at customs'); 
    }

    /**
     * Update the cleared_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cleared(Request $request, $id)
    {
        $validated = $request->validate([
            'cleared_at' => 'required|date',
            'customs_exchange_rate' => 'required',
            
        ]);
        $order = PurchaseOrder::find($id);
        $order->cleared_at = $request->get('cleared_at');
        $order->customs_exchange_rate = $request->get('customs_exchange_rate');
        $order->customs_duty = $order->amount_inr_customs * \Setting::get('purchase_order.customs_duty') / 100;
        $order->social_welfare_surcharge = $order->customs_duty * \Setting::get('purchase_order.social_welfare_surcharge') / 100;
        $order->igst = ($order->amount_inr + $order->customs_duty + $order->social_welfare_surcharge )* \Setting::get('purchase_order.igst') / 100;
        $order->bank_and_transport_charges = $order->amount_inr * \Setting::get('purchase_order.transport') / 100;
        $charges = [
            'customs_duty'=> \Setting::get('purchase_order.customs_duty'),
            'social_welfare_surcharge'=> \Setting::get('purchase_order.social_welfare_surcharge'),
            'igst'=> \Setting::get('purchase_order.igst'),
            'transport'=> \Setting::get('purchase_order.transport'),
        ];
        $order->charges = json_encode($charges);
        $order->status = PurchaseOrder::CLEARED;
        $order->update();
        return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order cleared'); 
    }

    /**
     * Update the shipped_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function received(Request $request, $id)
    {
        $order = PurchaseOrder::find($id);
        $order->received_at = $request->get('received_at');
        $order->status = PurchaseOrder::RECEIVED;
        $order->update();

        $inventory = new Inventory();
        $inventory->updateStock($order);

        return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order received'); 
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
        if ($user->can('delete purchase orders')){
            $order = PurchaseOrder::find($id);
            $order->items()->delete();
            $order->delete();
            return redirect(route('purchase-orders'))->with('success', trans('app.record_deleted', ['field' => 'Purchase Order']));
        }
        return abort(403, trans('error.unauthorized'));

    }
}