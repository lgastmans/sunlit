<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inventory;

class InventoryController extends Controller
{

    public function index()
    {
  
        return view('inventory.index');
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

        $order_column = 'products.name';
        $order_dir = 'ASC';
        $order_arr = array();
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            $order_column = $column_arr[$column_index]['data'];
            $order_dir = $order_arr[0]['dir'];
        }
        $order_column = 'stock_available';

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Inventory::count();
        $totalRecordswithFilter = Inventory::with('product')
                ->join('products', 'products.id', '=', 'product_id')
                ->select('inventories.*', 'products.code', 'products.name')
                ->where('products.name', 'like', '%'.$search.'%')
                ->orWhere('products.code', 'like', '%'.$search.'%')
                ->count();

    
        // Fetch records
        if ($length < 0)
            $inventory = Inventory::with('product')
                ->select('inventories.*', 'products.code', 'products.name')
                ->join('products', 'products.id', '=', 'product_id')
                ->where('products.name', 'like', '%'.$search.'%')
                ->orWhere('products.code', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        else
            $inventory = Inventory::with('product')
                ->select('inventories.*', 'products.code', 'products.name')
                ->join('products', 'products.id', '=', 'product_id')
                ->where('products.name', 'like', '%'.$search.'%')
                ->orWhere('products.code', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                //->toSql();
                ->get();

//dd($inventory);

        $arr = array();

        foreach ($inventory as $record)
        {
            $arr[] = array(
                "id" => $record->id,
                "code" => $record->product->code,
                "name" => $record->product->name,
                "available" => $record->stock_available,
                "booked" => $record->stock_booked,
                "ordered" => $record->stock_ordered,
                "projected" => ($record->stock_available - $record->stock_booked)
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
