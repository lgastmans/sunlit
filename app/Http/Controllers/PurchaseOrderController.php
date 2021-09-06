<?php

namespace App\Http\Controllers;

use \NumberFormatter;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
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
        // if ($user->can('list purchase orders'))
            return view('purchase_orders.index');
    
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
        $totalRecords = PurchaseOrder::get()->count();
        $totalRecordswithFilter = PurchaseOrder::join('warehouses', 'warehouses.id', '=', 'purchase_orders.warehouse_id')
            ->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->join('users', 'users.id', '=', 'purchase_orders.user_id')
            ->where('order_number', 'like', '%'.$search.'%')
            ->orWhere('suppliers.company', 'like', '%'.$search.'%')
            ->get()
            ->count();
        

        // Fetch records
        if ($length < 0){
            $po = $po = PurchaseOrder::select('purchase_orders.*', 'suppliers.company', 'users.name')
                ->join('warehouses', 'warehouses.id', '=', 'purchase_orders.warehouse_id')
                ->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')
                ->join('users', 'users.id', '=', 'purchase_orders.user_id')
                ->where('order_number', 'like', '%'.$search.'%')
                ->orWhere('suppliers.company', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        }
        else{
            $po = PurchaseOrder::select('purchase_orders.*', 'suppliers.company', 'users.name')
                ->join('warehouses', 'warehouses.id', '=', 'purchase_orders.warehouse_id')
                ->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')
                ->join('users', 'users.id', '=', 'purchase_orders.user_id')
                ->where('order_number', 'like', '%'.$search.'%')
                ->orWhere('suppliers.company', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();

        }
        $arr = array();
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        foreach($po as $record)
        {
            if ($record->status==1)
                $status = '<span class="badge badge-secondary-lighten">Draft</span>';
            elseif ($record->status==2)
                $status = '<span class="badge badge-info-lighten">Ordered</span>';
            elseif ($record->status==3)
                $status = '<span class="badge badge-primary-lighten">Confirmed</span>';
            elseif ($record->status==4)
                $status = '<span class="badge badge-dark-lighten">Shipped</span>';
            elseif ($record->status==5)
                $status = '<span class="badge badge-warning-lighten">Customs</span>';
            elseif ($record->status==6)
                $status = '<span class="badge badge-light-lighten">Cleared</span>';
            elseif ($record->status==7)
                $status = '<span class="badge badge-success-lighten">Received</span>';
            else
                $status = '<span class="badge badge-error-lighten">Unknown</span>';

            $arr[] = array(
                "id" => $record->id,
                "order_number" => $record->order_number,
                "supplier" => $record->company,
                "ordered_at" => (is_null($record->ordered_at)? '' : $record->ordered_at->format('d/M/Y')),
                "expected_at" => (is_null($record->expected_at)? '' : $record->expected_at->format('d/M/Y')),
                "received_at" => (is_null($record->received_at)? '' : $record->received_at->format('d/M/Y'))    ,
                "amount_inr" => $fmt->format($record->amount_inr/100),
                "status" => $status,
                "warehouse" => $record->warehouse->name,
                "user" => ucfirst($record->name)
            );
        }

        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $arr,
            'error' => null
        );
        echo json_encode($response);

        exit;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $purchase_order = new PurchaseOrder();
        return view('purchase_orders.form', ['purchase_order' => $purchase_order]);
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
        $purchase_order = PurchaseOrder::create($validatedData);
        if ($purchase_order) {
            //Save the purchase an redirect to the cart view with order_id
            return redirect()->action(
                [PurchaseOrderController::class, 'cart'], ['id' => $purchase_order->id]
            );
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
        $purchase_order = PurchaseOrder::with(['supplier','warehouse','items'])->where('order_number', '=', $order_number)->first();
        if ($purchase_order){
            if ($purchase_order->status == PurchaseOrder::DRAFT)
                return view('purchase_orders.cart', ['purchase_order' => $purchase_order ]);

            return view('purchase_orders.show', ['purchase_order' => $purchase_order ]);
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
        $purchase_order = PurchaseOrder::with(['supplier', 'warehouse', 'items', 'items.product'])->where('order_number', '=', $order_number)->first();
        if ($purchase_order)
            return view('purchase_orders.show', ['purchase_order' => $purchase_order ]);

        return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'purchase order']));
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
            //check for dupes
            return json_encode(['code'=>200, 'message'=> 'OK', 'field' => $request->get('field')]); 
        }
        if ($request->get('field') == "warehouse"){
            $warehouse = Warehouse::find($request->get('warehouse_id'));
            return json_encode(['code'=>200, 'message'=> 'OK', 'field' => $request->get('field'), 'warehouse'=> $warehouse]);
        }
        
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