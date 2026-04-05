<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use NumberFormatter;

class SaleOrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

public function getListForDatatables(Request $request): JsonResponse
{
    $fmt = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
    $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

    $draw   = $request->get('draw', 1);
    $start  = $request->get('start', 0);
    $length = $request->get('length', 10);

    if (! $request->has('filter_product_id')) {
        return response()->json([]);
    }

    $filter_product_id = $request->get('filter_product_id');

    // Base query
    $query = SaleOrderItem::query()
        ->join('sale_orders', 'sale_orders.id', '=', 'sale_order_items.sale_order_id')
        ->join('users', 'users.id', '=', 'sale_orders.user_id')
        ->join('warehouses', 'warehouses.id', '=', 'sale_orders.warehouse_id')
        ->join('dealers', 'dealers.id', '=', 'sale_orders.dealer_id')
        ->where('sale_order_items.product_id', $filter_product_id)
        ->select([
            'sale_order_items.id',
            'sale_orders.order_number',
            'sale_orders.order_number_slug',
            'sale_orders.status',
            'sale_orders.booked_at',
            'sale_orders.dispatched_at',
            'sale_orders.created_at',
            'sale_order_items.quantity_ordered',
            'sale_order_items.selling_price',
            'warehouses.name as warehouse',
            'dealers.company as dealer',
            'users.name as user',
        ]);

    // Total count
    $totalRecords = (clone $query)->count();

    // 🔍 Filtering (cleaner)
    $columns = $request->get('columns', []);

    if (!empty($columns[0]['search']['value'])) {
        $query->where('sale_orders.order_number', 'like', '%'.$columns[0]['search']['value'].'%');
    }

    if (!empty($columns[1]['search']['value'])) {
        $query->where('warehouses.name', 'like', '%'.$columns[1]['search']['value'].'%');
    }

    if (!empty($columns[2]['search']['value'])) {
        $query->where('dealers.company', 'like', '%'.$columns[2]['search']['value'].'%');
    }

    if (!empty($columns[3]['search']['value'])) {
        $query->where('sale_order_items.quantity_ordered', 'like', $columns[3]['search']['value'].'%');
    }

    if (!empty($columns[5]['search']['value'])) {
        $query->where('sale_orders.status', $columns[5]['search']['value']);
    }

    if (!empty($columns[6]['search']['value'])) {
        $query->where('sale_orders.booked_at', 'like', convertDateToMysql($columns[6]['search']['value']));
    }

    if (!empty($columns[7]['search']['value'])) {
        $query->where('sale_orders.dispatched_at', 'like', convertDateToMysql($columns[7]['search']['value']));
    }

    if (!empty($columns[8]['search']['value'])) {
        $query->where('sale_orders.created_at', 'like', convertDateToMysql($columns[8]['search']['value']));
    }

    if (!empty($columns[9]['search']['value'])) {
        $query->where('users.name', 'like', $columns[9]['search']['value'].'%');
    }

    // Filtered count
    $totalRecordsFiltered = (clone $query)->count();

    // 🔽 Ordering (clean mapping)
    $orderColumnMap = [
        0 => 'sale_orders.order_number',
        1 => 'warehouses.name',
        2 => 'dealers.company',
        3 => 'sale_order_items.quantity_ordered',
        5 => 'sale_orders.status',
        6 => 'sale_orders.booked_at',
        7 => 'sale_orders.dispatched_at',
        8 => 'sale_orders.created_at',
        9 => 'users.name',
    ];

    if ($request->has('order')) {
        $order = $request->get('order')[0];
        $columnIndex = $order['column'];
        $dir = $order['dir'];

        if (isset($orderColumnMap[$columnIndex])) {
            $query->orderBy($orderColumnMap[$columnIndex], $dir);
        }
    }

    // Paging
    if ($length > 0) {
        $query->skip($start)->take($length);
    }

    $orders = $query->get();

    // 🎯 Transform data cleanly
    $data = $orders->map(function ($order) use ($fmt) {

        // Status + display date
        switch ($order->status) {
            case SaleOrder::DRAFT:
                $status = '<span class="badge badge-secondary-lighten">Draft</span>';
                $display_date = $order->created_at;
                break;

            case SaleOrder::BOOKED:
                $status = '<span class="badge badge-primary-lighten">Booked</span>';
                $display_date = $order->booked_at;
                break;

            case SaleOrder::DISPATCHED:
                $status = '<span class="badge badge-dark-lighten">Dispatched</span>';
                $display_date = $order->dispatched_at;
                break;

            default:
                $status = '<span class="badge badge-warning-lighten">Unknown</span>';
                $display_date = null;
        }

        return [
            'order_number' => $order->order_number,
            'order_number_slug' => $order->order_number_slug,
            'warehouse' => $order->warehouse,
            'dealer' => $order->dealer,
            'quantity_ordered' => number_format($order->quantity_ordered, 0),
            'selling_price' => $fmt->formatCurrency($order->selling_price, 'INR'),
            'status' => $status,

            // ✅ now consistent with DataTables
            'booked_at' => optional($order->booked_at)->format('d M Y'),
            'dispatched_at' => optional($order->dispatched_at)->format('d M Y'),
            'created_at' => optional($order->created_at)->format('d M Y'),

            'user' => $order->user,
        ];
    });

    return response()->json([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecordsFiltered,
        'data' => $data,
    ]);
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
     */
    public function store(Request $request): JsonResponse
    {
        $product = Product::find($request->get('product_id'));

        if ($product) {

            $item = SaleOrderItem::where('sale_order_id', '=', $request->sale_order_id)->where('product_id', '=', $request->product_id)->first();

            $order = SaleOrder::find($request->sale_order_id);

            $inventory = new Inventory;

            if ($item) {
                $item->quantity_ordered = $request->quantity_ordered;
                $item->update();

                $inventory->updateItemStock($order, $item->product_id, ($request->quantity_ordered - $item->quantity_ordered));
            } else {
                $item = new SaleOrderItem;
                $item->sale_order_id = $request->sale_order_id;
                $item->product_id = $request->product_id;
                $item->tax = $product->tax->amount;
                $item->quantity_ordered = $request->quantity_ordered;
                $item->selling_price = $request->selling_price;
                $item->save();

                $inventory->updateItemStock($order, $item->product_id, $request->quantity_ordered);
            }

            return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'item' => $item, 'product' => $product]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(SaleOrderItem $saleOrderItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(SaleOrderItem $saleOrderItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $item = SaleOrderItem::find($id);

        $order = SaleOrder::find($item->sale_order_id);

        $update_quantity = 0;

        if ($request->field == 'quantity') {

            if ($order->status == SaleOrder::DRAFT) {
                $item->quantity_ordered = $request->value;
            } else {
                $update_quantity = $request->value - $item->quantity_ordered;

                $item->quantity_ordered = $request->value;

                $inventory = new Inventory;
                $inventory->updateItemStock($order, $item->product_id, $update_quantity);
            }
        }

        if ($request->field == 'price') {
            $item->selling_price = $request->value;
        }

        $item->update();

        return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $item = SaleOrderItem::find($id);

        $order = SaleOrder::find($item->sale_order_id);

        $inventory = new Inventory;
        $inventory->updateItemStock($order, $item->product_id, ($item->quantity_ordered * -1));

        SaleOrderItem::destroy($id);

        $order = SaleOrder::find($item->sale_order_id);
        $order->amount = 0;
        foreach ($order->items as $item) {
            $order->amount += $item->total_price;
        }
        $order->update();

        return redirect(route('sale-orders.cart', $order->order_number_slug))->with('success', trans('app.record_deleted', ['field' => 'item']));

    }

    public static function getNumberAndTotalSaleByRange($range): JsonResponse
    {
        $query = DB::table('sale_order_items')
            ->join('products', 'products.id', '=', 'sale_order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.name as category', 'sale_order_items.created_at', DB::raw('count(DISTINCT (sale_order_items.sale_order_id)) as number_sale'), DB::raw('sum((sale_order_items.quantity_ordered * sale_order_items.selling_price)) as total_sale'));

        if ($range == 'daily') {
            $query->addSelect(DB::raw("DATE_FORMAT(sale_order_items.created_at, '%Y-%m-%d') date_range"), DB::raw('YEAR(sale_order_items.created_at) year, MONTH(sale_order_items.created_at) month, DAY(sale_order_items.created_at) day'))
                ->groupByRaw('products.category_id, year, month, day')
                ->OrderByRaw('year, month, day, products.category_id');
        }

        if ($range == 'monthly') {
            $query->addSelect(DB::raw("DATE_FORMAT(sale_order_items.created_at, '%Y-%m') date_range"), DB::raw('YEAR(sale_order_items.created_at) year, MONTH(sale_order_items.created_at) month'))
                ->groupByRaw('products.category_id, year, month')
                ->OrderByRaw('year, month,products.category_id');
        }
        if ($range == 'yearly') {
            $query->addSelect(DB::raw("DATE_FORMAT(sale_order_items.created_at, '%Y') date_range"), DB::raw('YEAR(sale_order_items.created_at) year'))
                ->groupByRaw('products.category_id, year')
                ->OrderByRaw('year, products.category_id');
        }

        $sales = $query->get();
        $categories = $sales->pluck('category')->unique();
        $series = [];
        foreach ($categories as $cat) {
            $series[Str::lower(Str::replace(' ', '-', $cat))]['name'] = $cat;
            $series[Str::lower(Str::replace(' ', '-', $cat))]['data'] = [];
        }
        // dd($series);
        foreach ($sales as $sale) {
            $data_point['x'] = \Carbon\Carbon::parse($sale->created_at)->timestamp;
            $data_point['y'] = $sale->total_sale;
            array_push($series[Str::lower(Str::replace(' ', '-', $sale->category))]['data'], $data_point);
        }

        // dd($series);
        return response()->json(['series' => $series]);
    }
}
