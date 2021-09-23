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
            $order_column = $column_arr[$column_index]['data'];
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

        $query->with('product')
                ->with('warehouse')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_id')
                ->select('inventory_movements.*', 'purchase_orders.order_number AS order_number');

        $query->where('product_id', '=', $filter_product_id);

        $query->orderBy($order_column, $order_dir);

        // if ($length > 0)
        //     $query->skip($start)->take($length);

        //$movement = $query->toSql();dd($movement);
        $movement = $query->get();


        foreach ($movement as $record)
        {

            $arr[] = array(
                "id" => $record->id,
                "created_at" => $record->created_at,
                "order_number" => $record->order_number,
                "quantity" => $record->quantity,
                "entry_type" => ($record->movement_type == InventoryMovement::RECEIVED) ? "<span class='badge badge-danger-lighten'>Received</span>" : "<span class='badge badge-danger-lighten'>Delivered</span>",
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

        echo json_encode($response);
        exit;
    }

}
