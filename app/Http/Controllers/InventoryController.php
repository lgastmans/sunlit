<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;
use App\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class InventoryController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if ($user->can('list inventories')) {
            $stock_filter = Inventory::getStockFilterList();
            return view('inventory.index', ['stock_filter' => $stock_filter]);
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

        $order_column = 'products.code';
        $order_dir = 'ASC';
        $order_arr = array();
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            switch ($column_index){
                case '0':
                    $order_column = "warehouses.name";
                    break;
                case '1':
                    $order_column = "categories.name";
                    break;
                case '2':
                    $order_column = "suppliers.company";
                    break;
                case '3':
                    $order_column = "products.code";
                    break;
                case '4':
                    $order_column = "products.name";
                    break;
                default:
                $order_column = $column_arr[$column_index]['data'];    
            }                       
            
            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Inventory::count();
    
        /*
            build the query
        */
        $query = Inventory::query();

        $query->with(['product','warehouse'])
                ->select('inventories.*', 'products.code', 'products.name', 'products.minimum_quantity', 'suppliers.company', 'categories.name', 'warehouses.name')
                ->addSelect(DB::raw('(inventories.stock_available + inventories.stock_ordered - inventories.stock_blocked - inventories.stock_booked) AS projected'))
                ->join('products', 'products.id', '=', 'product_id')
                ->join('warehouses', 'warehouses.id', '=', 'warehouse_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id');

        if (!empty($column_arr[0]['search']['value']))
            $query->where('warehouses.name', 'like', '%'.$column_arr[0]['search']['value'].'%');

        if (!empty($column_arr[1]['search']['value']))
            $query->where('categories.name', 'like', '%'.$column_arr[1]['search']['value'].'%');

        if (!empty($column_arr[2]['search']['value']))
            $query->where('suppliers.company', 'like', '%'.$column_arr[2]['search']['value'].'%');

        if (!empty($column_arr[3]['search']['value']))
            $query->where('products.part_number', 'like', '%'.$column_arr[3]['search']['value'].'%');

        if (!empty($column_arr[4]['search']['value'])){
            $filter_available = $column_arr[4]['search']['value'];
            if ($filter_available == '__BELOW_MIN_')
                $query->whereColumn('inventories.stock_available', '<=', 'products.minimum_quantity');
            elseif ($filter_available == '__NONE_ZERO_')
                $query->where('inventories.stock_available', '>', '0');
            elseif ($filter_available == '__ZERO_')                    
                $query->where('inventories.stock_available', '=', '0');
        }

        if (!empty($column_arr[5]['search']['value'])){
            $filter_ordered = $column_arr[5]['search']['value'];
            if ($filter_ordered == '__BELOW_MIN_')
                $query->whereColumn('inventories.stock_ordered', '<=', 'products.minimum_quantity');
            elseif ($filter_ordered == '__NONE_ZERO_')
                $query->where('inventories.stock_ordered', '>', '0');
            elseif ($filter_ordered == '__ZERO_')                    
                $query->where('inventories.stock_ordered', '=', '0');
        }

        if (!empty($column_arr[6]['search']['value'])){
            $filter_booked = $column_arr[6]['search']['value'];
            if ($filter_booked == '__BELOW_MIN_')
                $query->whereColumn('inventories.stock_blocked', '<=', 'products.minimum_quantity');
            elseif ($filter_booked == '__NONE_ZERO_')
                $query->where('inventories.stock_blocked', '>', '0');
            elseif ($filter_booked == '__ZERO_')                    
                $query->where('inventories.stock_blocked', '=', '0');
        }

        if (!empty($column_arr[7]['search']['value'])){
            $filter_booked = $column_arr[7]['search']['value'];
            if ($filter_booked == '__BELOW_MIN_')
                $query->whereColumn('inventories.stock_booked', '<=', 'products.minimum_quantity');
            elseif ($filter_booked == '__NONE_ZERO_')
                $query->where('inventories.stock_booked', '>', '0');
            elseif ($filter_booked == '__ZERO_')                    
                $query->where('inventories.stock_booked', '=', '0');
        }

        if (!empty($column_arr[8]['search']['value'])){
            $filter_projected = $column_arr[8]['search']['value'];
            if ($filter_projected == '__BELOW_MIN_')
                $query->havingRaw('projected <= products.minimum_quantity');
            elseif ($filter_projected == '__NONE_ZERO_')
                $query->havingRaw('projected > 0');
            elseif ($filter_projected == '__ZERO_')                    
                $query->havingRaw('projected = 0');
        }

        /*
        $query->where( function ($q) use ($filter_available, $filter_ordered, $filter_booked, $filter_projected){

            if ($filter_available == '__BELOW_MIN_')
                $q->whereColumn('inventories.stock_available', '<=', 'products.minimum_quantity');
            elseif ($filter_available == '__NONE_ZERO_')
                $q->where('inventories.stock_available', '>', '0');
            elseif ($filter_available == '__ZERO_')                    
                $q->where('inventories.stock_available', '=', '0');

            if ($filter_ordered == '__BELOW_MIN_')
                $q->whereColumn('inventories.stock_ordered', '<=', 'products.minimum_quantity');
            elseif ($filter_ordered == '__NONE_ZERO_')
                $q->where('inventories.stock_ordered', '>', '0');
            elseif ($filter_ordered == '__ZERO_')                    
                $q->where('inventories.stock_ordered', '=', '0');

            if ($filter_booked == '__BELOW_MIN_')
                $q->whereColumn('inventories.stock_booked', '<=', 'products.minimum_quantity');
            elseif ($filter_booked == '__NONE_ZERO_')
                $q->where('inventories.stock_booked', '>', '0');
            elseif ($filter_booked == '__ZERO_')                    
                $q->where('inventories.stock_booked', '=', '0');

            if ($filter_projected == '__BELOW_MIN_')
                $q->havingRaw('projected <= products.minimum_quantity');
            elseif ($filter_projected == '__NONE_ZERO_')
                $q->havingRaw('projected > 0');
            elseif ($filter_projected == '__ZERO_')                    
                $q->havingRaw('projected = 0');

        });
        */

        if ($request->has('search')){
            
            $search = $request->get('search')['value'];

            $query->where( function ($q) use ($search)
            {
                $q->where('products.part_number', 'like', '%'.$search.'%')
                    ->orWhere('warehouses.name', 'like', '%'.$search.'%')
                    ->orWhere('categories.name', 'like', '%'.$search.'%')
                    ->orWhere('suppliers.company', 'like', '%'.$search.'%');
            });    
        }

        $query->orderBy($order_column, $order_dir);

        $totalRecordswithFilter = $query->count();

        if ($length > 0)
            $query->skip($start)->take($length);

        // $inventory = $query->toSql();dd($inventory);
        $inventory = $query->get();

        $arr = array();
        foreach ($inventory as $record)
        {

            $arr[] = array(
                "id" => $record->id,
                "supplier" => $record->company,
                "warehouse" => $record->warehouse->name,
                "category" => $record->product->category->name,
                // "code" => $record->product->code,
                // "name" => $record->product->name,
                "part_number" => $record->product->part_number,
                "available" => (object)$record->stock_available,
                "ordered" => $record->stock_ordered,
                "blocked" => $record->stock_blocked,
                "booked" => $record->stock_booked,
                "projected" => $record->projected, //($record->stock_available + $record->stock_ordered - $record->stock_booked),
                "minimum_quantity" => (object)$record->minimum_quantity,
                "product_id" => $record->product->id
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

    public function getExportList()
    {
        $current = Carbon::now()->format('YmdHs');

        return Excel::download(new InventoryExport, 'inventory_'.$current.'.xlsx');
    }

}
