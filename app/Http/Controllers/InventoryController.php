<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\SaleOrder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use NumberFormatter;

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
        $sale_order = new SaleOrder;

        $draw = 1;
        if ($request->has('draw')) {
            $draw = $request->get('draw');
        }

        $start = 0;
        if ($request->has('start')) {
            $start = $request->get('start');
        }

        $length = 10;
        if ($request->has('length')) {
            $length = $request->get('length');
        }

        $order_column = 'products.part_number';
        $order_dir = 'DESC';
        $order_arr = [];
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            switch ($column_index) {
                case '0':
                    $order_column = 'warehouses.name';
                    break;
                case '1':
                    $order_column = 'categories.name';
                    break;
                case '2':
                    $order_column = 'suppliers.company';
                    break;
                case '3':
                    $order_column = 'products.part_number';
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

        $query->with(['product', 'warehouse'])
            ->select('inventories.*', 'products.code', 'products.notes', 'products.name AS products_name', 'products.minimum_quantity', 'suppliers.company', 'categories.name AS categories_name', 'warehouses.name AS warehouses_name')
            ->addSelect(DB::raw('(inventories.stock_available + inventories.stock_ordered - inventories.stock_booked) AS projected'))
            ->join('products', 'products.id', '=', 'product_id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id');

        if (! empty($column_arr[0]['search']['value'])) {
            $query->where('warehouses.name', 'like', '%'.$column_arr[0]['search']['value'].'%');
        }

        if (! empty($column_arr[1]['search']['value'])) {
            $query->where('categories.name', 'like', '%'.$column_arr[1]['search']['value'].'%');
        }

        if (! empty($column_arr[2]['search']['value'])) {
            $query->where('suppliers.company', 'like', '%'.$column_arr[2]['search']['value'].'%');
        }

        if (! empty($column_arr[3]['search']['value'])) {
            $query->where('products.part_number', 'like', '%'.$column_arr[3]['search']['value'].'%');
        }

        if (! empty($column_arr[4]['search']['value'])) {
            $filter_available = $column_arr[4]['search']['value'];
            if ($filter_available == '__BELOW_MIN_') {
                $query->whereColumn('inventories.stock_available', '<=', 'products.minimum_quantity');
            } elseif ($filter_available == '__NONE_ZERO_') {
                $query->where('inventories.stock_available', '>', '0');
            } elseif ($filter_available == '__ZERO_') {
                $query->where('inventories.stock_available', '=', '0');
            }
        }

        if (! empty($column_arr[5]['search']['value'])) {
            $filter_ordered = $column_arr[5]['search']['value'];
            if ($filter_ordered == '__BELOW_MIN_') {
                $query->whereColumn('inventories.stock_ordered', '<=', 'products.minimum_quantity');
            } elseif ($filter_ordered == '__NONE_ZERO_') {
                $query->where('inventories.stock_ordered', '>', '0');
            } elseif ($filter_ordered == '__ZERO_') {
                $query->where('inventories.stock_ordered', '=', '0');
            }
        }

        if (! empty($column_arr[6]['search']['value'])) {
            $filter_booked = $column_arr[6]['search']['value'];
            if ($filter_booked == '__BELOW_MIN_') {
                $query->whereColumn('inventories.stock_blocked', '<=', 'products.minimum_quantity');
            } elseif ($filter_booked == '__NONE_ZERO_') {
                $query->where('inventories.stock_blocked', '>', '0');
            } elseif ($filter_booked == '__ZERO_') {
                $query->where('inventories.stock_blocked', '=', '0');
            }
        }

        if (! empty($column_arr[7]['search']['value'])) {
            $filter_booked = $column_arr[7]['search']['value'];
            if ($filter_booked == '__BELOW_MIN_') {
                $query->whereColumn('inventories.stock_booked', '<=', 'products.minimum_quantity');
            } elseif ($filter_booked == '__NONE_ZERO_') {
                $query->where('inventories.stock_booked', '>', '0');
            } elseif ($filter_booked == '__ZERO_') {
                $query->where('inventories.stock_booked', '=', '0');
            }
        }

        if (! empty($column_arr[8]['search']['value'])) {
            $filter_projected = $column_arr[8]['search']['value'];
            if ($filter_projected == '__BELOW_MIN_') {
                $query->havingRaw('projected <= products.minimum_quantity');
            } elseif ($filter_projected == '__NONE_ZERO_') {
                $query->havingRaw('projected > 0');
            } elseif ($filter_projected == '__ZERO_') {
                $query->havingRaw('projected = 0');
            }
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

        if ($request->has('search')) {

            $search = $request->get('search')['value'];

            $query->where(function ($q) use ($search) {
                $q->where('products.part_number', 'like', '%'.$search.'%')
                    ->orWhere('warehouses.name', 'like', '%'.$search.'%')
                    ->orWhere('categories.name', 'like', '%'.$search.'%')
                    ->orWhere('suppliers.company', 'like', '%'.$search.'%');
            });
        }

        $query->orderBy($order_column, $order_dir);

        $totalRecordswithFilter = $query->count();

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        // $inventory = $query->toSql();dd($inventory);
        $inventory = $query->get();

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $arr = [];
        foreach ($inventory as $record) {
            $overall_sales = $sale_order->calculateProductOverallSales($record->id);

            $arr[] = [
                'id' => $record->id,
                'total_sales' => intval($overall_sales),
                'supplier' => $record->company,
                'warehouse' => $record->warehouses_name, //$record->warehouse->name,
                'category' => $record->categories_name, //$record->product->category->name,
                // "code" => $record->product->code,
                // "name" => $record->product->name,
                'part_number' => $record->product->part_number.'<br><small>'.$record->product->notes.'</small>',
                'available' => (object) $record->stock_available,
                'ordered' => $record->stock_ordered,
                'blocked' => $record->stock_blocked,
                'booked' => $record->stock_booked,
                'projected' => $record->projected, // projected column is calculated in the SQL above
                'minimum_quantity' => (object) $record->minimum_quantity,
                'product_id' => $record->product->id,
            ];
        }

        usort($arr, fn ($a, $b) => $b['total_sales'] <=> $a['total_sales']);

        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i]['total_sales'] = $fmt->formatCurrency(intval($arr[$i]['total_sales']), 'INR');
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordswithFilter,
            'data' => $arr,
            'error' => null,
        ];

        echo json_encode($response);
        exit;
    }

    public function getExportList()
    {
        $current = Carbon::now()->format('YmdHs');

        return Excel::download(new InventoryExport, 'inventory_'.$current.'.xlsx');
    }

    public function resetOrderedStock(): JsonResponse
    {
        /**
         * current ordered column is calculated based on
         * POs that have status > CONFIRMED
         * minus
         * POIs that have status >= RECEIVED
         */
        $res = [];

        $inventory = new Inventory;

        $products = Product::all();

        foreach ($products as $product) {
            $arr = [];

            //if ($product->id == 566) {

            foreach ($product->inventory as $entry) {

                $ordered = $inventory->getStockOrdered($product->id, $entry->warehouse_id);

                if ($ordered) {
                    //echo $entry->warehouse_id.">>".$ordered->ordered_stock.":".$received->received_stock."<br>";
                    $received = $inventory->getStockReceived($product->id, $entry->warehouse_id);

                    if ($received) {
                        $updated_stock = $ordered->ordered_stock - $received->received_stock;

                        $inv = Inventory::find($entry->id);

                        if ($inv) {
                            $inv->stock_ordered = $updated_stock;
                            $inv->save();
                        }
                    }
                } else {
                    // set to zero
                    $inv = Inventory::find($entry->id);

                    if ($inv) {
                        $inv->stock_ordered = 0;
                        $inv->save();
                    }
                }
            }
            //}
        }

        return response()->json($arr);

    }
}
