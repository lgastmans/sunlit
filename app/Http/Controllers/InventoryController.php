<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;

class InventoryController extends Controller
{

    public function index()
    {
  
        /*
        $received = PurchaseOrder::received()
            ->selectRaw("purchase_order_items.product_id as id, SUM(purchase_order_items.quantity_received) as total_received")
            ->join('purchase_order_items', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->groupBy('purchase_order_items.product_id')
            ->get();

        //dd($received);

        $received = PurchaseOrder::received()
            ->join('purchase_order_items', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->groupBy('purchase_order_items.product_id')
            ->sum('purchase_order_items.quantity_received');
        */



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

        $order_column = 'name';
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

        // Total records
        $totalRecords = PurchaseOrder::received()
            ->selectRaw("purchase_order_items.product_id AS product_id, products.code, products.name, SUM(purchase_order_items.quantity_received) AS total_received")
            ->join('purchase_order_items', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('products', 'products.id', '=', 'purchase_order_items.product_id')
            ->groupBy('purchase_order_items.product_id')
            ->get()
            ->count();
        $totalRecordswithFilter = $totalRecords;

    
        // Fetch records
        if ($length < 0)
            $inventory = PurchaseOrder::received()
                ->selectRaw("purchase_order_items.product_id AS product_id, products.code, products.name, SUM(purchase_order_items.quantity_received) AS total_received")
                ->join('purchase_order_items', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->join('products', 'products.id', '=', 'purchase_order_items.product_id')
                ->groupBy('purchase_order_items.product_id')
                ->get();
        else
            $inventory = PurchaseOrder::received()
                ->selectRaw("purchase_order_items.product_id AS product_id, products.code, products.name, SUM(purchase_order_items.quantity_received) AS total_received")
                ->join('purchase_order_items', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->join('products', 'products.id', '=', 'purchase_order_items.product_id')
                ->groupBy('purchase_order_items.product_id')
                ->skip($start)
                ->take($length)
                ->get();

        $arr = array();

        foreach ($inventory as $record)
        {
            $arr[] = array(
                "id" => $record->product_id,
                "code" => $record->code,
                "name" => $record->name,
                "available" => $record->total_received
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
