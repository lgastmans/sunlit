<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;

class InventoryController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if ($user->can('list inventories'))
            return view('inventory.index');
    
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

        $order_column = 'products.code';
        $order_dir = 'ASC';
        $order_arr = array();
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            
            if ($column_index==0)
                $order_column = "suppliers.company";
            elseif ($column_index==1)
                $order_column = "warehouses.name";
            elseif ($column_index==2)
                $order_column = "categories.name";
            elseif ($column_index==3)
                $order_column = "products.code";
            elseif ($column_index==4)
                $order_column = "products.name";
            else
                $order_column = $column_arr[$column_index]['data'];            
            
            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        $filter_min_qty = '__ALL_';
        if ($request->has('filterMinQty'))
        {
            $filter_min_qty = $request->get('filterMinQty');
        }

        // Total records
        $totalRecords = Inventory::count();
        $totalRecordswithFilter = Inventory::with('product')
                ->with('warehouse')
                ->join('products', 'products.id', '=', 'product_id')
                ->join('warehouses', 'warehouses.id', '=', 'warehouse_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
                ->select('inventories.*', 'products.code', 'products.name', 'suppliers.company', 'categories.name')
                ->where('products.name', 'like', '%'.$search.'%')
                ->orWhere('products.code', 'like', '%'.$search.'%')
                ->orWhere('warehouses.name', 'like', '%'.$search.'%')
                ->orWhere('categories.name', 'like', '%'.$search.'%')
                ->orWhere('suppliers.company', 'like', '%'.$search.'%')
                ->count();

    
        /*
            build the query
        */
        $query = Inventory::query();

        $query->with('product')
                ->with('warehouse')
                ->select('inventories.*', 'products.code', 'products.name', 'products.minimum_quantity', 'suppliers.company', 'categories.name')
                ->join('products', 'products.id', '=', 'product_id')
                ->join('warehouses', 'warehouses.id', '=', 'warehouse_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id');

        if ($filter_min_qty != '__ALL_') {
            $query->where( function ($q) use ($filter_min_qty){
                if ($filter_min_qty == '__BELOW_MIN_')
                    $q->whereColumn('inventories.stock_available', '<=', 'products.minimum_quantity');
                elseif ($filter_min_qty == '__NONE_ZERO_')
                    $q->where('inventories.stock_available', '>', '0');
                elseif ($filter_min_qty == '__ZERO_')                    
                    $q->where('inventories.stock_available', '=', '0');
            });    

        }

        if ($request->has('search')){
            
            $search = $request->get('search')['value'];

            $query->where( function ($q) use ($search)
            {
                $q->where('products.code', 'like', '%'.$search.'%')
                    ->orWhere('products.name', 'like', '%'.$search.'%')
                    ->orWhere('warehouses.name', 'like', '%'.$search.'%')
                    ->orWhere('categories.name', 'like', '%'.$search.'%')
                    ->orWhere('suppliers.company', 'like', '%'.$search.'%');
            });    
        }

        $query->orderBy($order_column, $order_dir);

        if ($length > 0)
            $query->skip($start)->take($length);

        // $inventory = $query->toSql();dd($inventory);
        $inventory = $query->get();



        // Fetch records
        /*
        if ($length < 0)
            $inventory = Inventory::with('product')
                ->with('warehouse')
                ->select('inventories.*', 'products.code', 'products.name', 'products.minimum_quantity', 'suppliers.company', 'categories.name')
                ->join('products', 'products.id', '=', 'product_id')
                ->join('warehouses', 'warehouses.id', '=', 'warehouse_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
                ->where('products.name', 'like', '%'.$search.'%')
                ->orWhere('products.code', 'like', '%'.$search.'%')
                ->orWhere('warehouses.name', 'like', '%'.$search.'%')
                ->orWhere('categories.name', 'like', '%'.$search.'%')
                ->orWhere('suppliers.company', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        else
            $inventory = Inventory::with('product')
                ->with('warehouse')
                ->select('inventories.*', 'products.code', 'products.name', 'products.minimum_quantity', 'suppliers.company', 'categories.name')
                ->join('products', 'products.id', '=', 'product_id')
                ->join('warehouses', 'warehouses.id', '=', 'warehouse_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
                ->where('products.name', 'like', '%'.$search.'%')
                ->orWhere('products.code', 'like', '%'.$search.'%')
                ->orWhere('warehouses.name', 'like', '%'.$search.'%')
                ->orWhere('categories.name', 'like', '%'.$search.'%')
                ->orWhere('suppliers.company', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                //->toSql();
                ->get();
        */


        $arr = array();

        foreach ($inventory as $record)
        {

            $arr[] = array(
                "id" => $record->id,
                "supplier" => $record->company,
                "warehouse" => $record->warehouse->name,
                "category" => $record->name,
                "code" => $record->product->code,
                "name" => $record->product->name,
                "available" => (object)$record->stock_available,
                "ordered" => $record->stock_ordered,
                "booked" => $record->stock_booked,
                "projected" => ($record->stock_available + $record->stock_ordered - $record->stock_booked),
                "minimum_quantity" => (object)$record->minimum_quantity
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
