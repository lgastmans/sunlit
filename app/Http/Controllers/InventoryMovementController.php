<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\InventoryMovement;

class InventoryMovementController extends Controller
{
    //

    
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list inventories')) {
            //$product = Product::with(['inventory', 'inventory.warehouse', 'movement', 'supplier'])->find($id);
            //if ($product)
                return view('inventory-movement.index', ['product'=>null]);

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'product']));
        }

        return abort(403, trans('error.unauthorized'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        if ($user->can('list inventories')) {
            $product = Product::with(['inventory', 'inventory.warehouse', 'movement', 'supplier'])->find($id);
            if ($product)
                return view('inventory-movement.index', ['product'=>$product]);

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'product']));
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

        $order_column = 'created_at';
        $order_dir = 'ASC';
        $order_arr = array();
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            if ($column_index==1)
                $order_column = "warehouses.name";
            else{
                $order_column = $column_arr[$column_index]['data'];
            }
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
        $totalRecords = InventoryMovement::where('product_id', '=', $filter_product_id)->count();
        $totalRecordswithFilter = InventoryMovement::with('product')
                ->where('product_id', '=', $filter_product_id)
                ->with('warehouse')
                ->count();
    
        /*
            build the query

            this query will have to be updated when sales orders are added, something along these lines:

                IF NOT purchase_order_id == null THEN SET order_number=purchase_orders.order_number
                    ELSEIF NOT sales_order_id == null THEN order_number=sales_orders.order_number
                END IF
                AS order_number

                Sometimes you may need to insert an arbitrary string into a query. To create a raw string expression, you may use the raw method provided by the DB facade:

                    ->select(DB::raw('count(*) as user_count, status'))

            or simply

                an IF() in the SELECT clause

        */
        $query = InventoryMovement::query();

        $query->with(['product','warehouse'])
                ->join('users', 'users.id', '=', 'user_id')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_id')
                ->leftJoin('warehouses', 'warehouses.id', '=', 'inventory_movements.warehouse_id')
                ->select('inventory_movements.*', 'purchase_orders.order_number AS order_number');

        if (!empty($column_arr[0]['search']['value']))
                $query->where('order_number', 'like', '%'.$column_arr[0]['search']['value'].'%');


        if (!empty($column_arr[1]['search']['value'])){
            if ($column_arr[1]['search']['value'] != '__ALL_')
                $query->where('inventory_movements.warehouse_id', '=', $column_arr[1]['search']['value']);
        }
        
        if (!empty($column_arr[2]['search']['value']))
            $query->where('quantity', '=', $column_arr[2]['search']['value']);
        

        if (!empty($column_arr[3]['search']['value'])){
            $query->where('movement_type', '=', $column_arr[3]['search']['value']);
        }


        if (!empty($column_arr[4]['search']['value']))
            $query->where('inventory_movements.created_at', 'like', convertDateToMysql($column_arr[4]['search']['value']).'%');

        if (!empty($column_arr[5]['search']['value']))
            $query->where('users.name', 'like', '%'.$column_arr[5]['search']['value'].'%');

        $query->where('product_id', '=', $filter_product_id);

        $query->orderBy($order_column, $order_dir);

        if ($length > 0)
            $query->skip($start)->take($length);

        $movement = $query->get();


        foreach ($movement as $record)
        {
            $arr[] = array(
                "id" => $record->id,
                "created_at" => $record->display_created_at,
                "order_number" => $record->order_number,
                "quantity" => $record->quantity,
                "entry_type" => $record->display_movement_type,
                "warehouse" => $record->warehouse->name,
                "user" => $record->user->name,
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

}
