<?php

namespace App\Http\Controllers;

use \NumberFormatter;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;


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
        if ($length < 0)
            $po = PurchaseOrder::join('warehouses', 'warehouses.id', '=', 'purchase_orders.warehouse_id')
                ->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')
                ->join('users', 'users.id', '=', 'purchase_orders.user_id')
                ->where('order_number', 'like', '%'.$search.'%')
                ->orWhere('suppliers.company', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        else
            $po = PurchaseOrder::join('warehouses', 'warehouses.id', '=', 'purchase_orders.warehouse_id')
                ->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')
                ->join('users', 'users.id', '=', 'purchase_orders.user_id')
                ->where('order_number', 'like', '%'.$search.'%')
                ->orWhere('suppliers.company', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();

        $arr = array();

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);

        foreach($po as $record)
        {
            $arr[] = array(
                "id" => $record->id,
                "order_number" => $record->order_number,
                "supplier" => $record->company,
                "ordered_at" => $record->ordered_at->format('d/M/Y'),
                "expected_at" => $record->expected_at->format('d/M/Y'),
                "received_at" => $record->received_at->format('d/M/Y'),
                "amount_inr" => $fmt->format($record->amount_inr/100),
                "status" => $record->status,
                "warehouse" => $record->warehouse->name,
                "user" => $record->name
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
        //
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


    public function test_purchase_orders()    
    {
        $purchase_order = PurchaseOrder::factory()
            // ->has(
            //     PurchaseOrderItem::factory()
            // )
            ->count(10)
            ->create();
    }

}