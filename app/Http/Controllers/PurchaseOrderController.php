<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\Inventory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderInvoiceItem;
use App\Models\PurchaseOrderItem;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumberFormatter;
use PDF;
use Spatie\Activitylog\Models\Activity;

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
        if ($user->can('list purchase orders')) {
            $status = PurchaseOrder::getStatusList();

            return view('purchase_orders.index', ['status' => $status]);

        }

        return abort(403, trans('error.unauthorized'));
    }

    public function filter($filter)
    {
        $user = Auth::user();
        if ($user->can('list purchase orders')) {
            $status = PurchaseOrder::getStatusList();

            return view('purchase_orders.index', ['status' => $status, 'filter' => $filter]);

        }

        return abort(403, trans('error.unauthorized'));
    }

    public function getListForDatatables(Request $request): JsonResponse
    {
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

        $order_column = 'order_number';
        $order_dir = 'ASC';
        $order_arr = [];
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];

            // the purchase order datatable isn't the same in index than in warehouse>purchase orders
            if ($request->has('source') && $request->source == 'warehouses') {
                switch ($column_index) {
                    case 1:
                        $order_column = 'suppliers.company';
                        break;
                    case 4:
                        $order_column = 'users.name';
                        break;

                    default:
                        $order_column = $column_arr[$column_index]['data'];
                }
            } else {
                switch ($column_index) {
                    case 1:
                        $order_column = 'purchase_orders.warehouse_id';
                        break;
                    case 2:
                        $order_column = 'suppliers.company';
                        break;
                    case 7:
                        $order_column = 'users.name';
                        break;
                    default:
                        $order_column = $column_arr[$column_index]['data'];
                }
            }
            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        $totalRecords = PurchaseOrder::count();

        $query = PurchaseOrder::query();
        $query->join('suppliers', 'suppliers.id', '=', 'supplier_id');
        $query->join('warehouses', 'warehouses.id', '=', 'warehouse_id');
        $query->join('users', 'users.id', '=', 'user_id');

        if ($request->has('filter_warehouse_id')) {
            $query->where('purchase_orders.warehouse_id', '=', $request->filter_warehouse_id);
        }
        if ($request->has('source') && $request->source == 'warehouses') {
            if (! empty($column_arr[0]['search']['value'])) {
                $query->where('purchase_orders.order_number', 'like', '%'.$column_arr[0]['search']['value'].'%');
            }
            if (! empty($column_arr[1]['search']['value'])) {
                $query->where('suppliers.company', 'like', '%'.$column_arr[1]['search']['value'].'%');
            }
            if (! empty($column_arr[2]['search']['value'])) {
                $query->where('purchase_orders.status', 'like', '%'.$column_arr[2]['search']['value']);
            }
            if (! empty($column_arr[3]['search']['value'])) {
                $query->where('purchase_orders.ordered_at', 'like', convertDateToMysql($column_arr[3]['search']['value']));
            }
            if (! empty($column_arr[4]['search']['value'])) {
                $query->where('users.name', 'like', $column_arr[4]['search']['value'].'%');
            }
        } else {
            if (! empty($column_arr[0]['search']['value'])) {
                $query->where('purchase_orders.order_number', 'like', '%'.$column_arr[0]['search']['value'].'%');
            }
            if (! empty($column_arr[1]['search']['value'])) {
                $query->where('warehouses.name', 'like', '%'.$column_arr[1]['search']['value'].'%');
            }
            if (! empty($column_arr[2]['search']['value'])) {
                $query->where('suppliers.company', 'like', '%'.$column_arr[2]['search']['value'].'%');
            }
            if (! empty($column_arr[3]['search']['value'])) {
                $query->where('purchase_orders.ordered_at', 'like', convertDateToMysql($column_arr[3]['search']['value']));
            }
            if (! empty($column_arr[4]['search']['value'])) {
                $query->where('purchase_orders.due_at', 'like', convertDateToMysql($column_arr[4]['search']['value']));
            }
            if (! empty($column_arr[5]['search']['value'])) {
                $query->where('purchase_orders.amount_inr', 'like', $column_arr[5]['search']['value'].'%');
            }
            if (! empty($column_arr[6]['search']['value']) && $column_arr[6]['search']['value'] != 'all') {
                $query->where('purchase_orders.status', 'like', $column_arr[6]['search']['value']);
            }
            if (! empty($column_arr[7]['search']['value'])) {
                $query->where('users.name', 'like', $column_arr[7]['search']['value'].'%');
            }
        }

        if ($request->filtered) {
            switch ($request->filtered) {
                case 'due':
                    $query->whereBetween('status', [PurchaseOrder::ORDERED, PurchaseOrder::CLEARED]);
                    break;
                case 'overdue':
                    $query->whereBetween('status', [PurchaseOrder::ORDERED, PurchaseOrder::CLEARED])->where('due_at', '<', Carbon::now());
                    break;
                default: '';
            }
        }

        if ($request->has('search')) {
            $search = $request->get('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('purchase_orders.order_number', 'like', '%'.$search.'%')
                    ->orWhere('purchase_orders.amount_inr', 'like', $search.'%')
                    ->orWhere('suppliers.company', 'like', '%'.$search.'%')
                    ->orWhere('users.name', 'like', '%'.$search.'%');
            });
        }

        if ($request->has('filter_column')) {
            $filter_column = $request->get('filter_column');
            $filter_from = $request->get('filter_from');
            $filter_to = $request->get('filter_to');

            if ((! is_null($filter_from)) && (! is_null($filter_to))) {
                $filter_from = Carbon::createFromFormat('Y-m-d', $filter_from)->toDateString();
                $filter_to = Carbon::createFromFormat('Y-m-d', $filter_to)->toDateString();

                if ($filter_column == 'ordered') {
                    $query->whereBetween('purchase_orders.ordered_at', [$filter_from, $filter_to]);
                } else {
                    $query->whereBetween('purchase_orders.due_at', [$filter_from, $filter_to]);
                }
            }
        }

        $totalRecordswithFilter = $query->count();

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $query->orderBy($order_column, $order_dir);
        \DB::enableQueryLog();
        $orders = $query->get();
        \Debugbar::info(\DB::getQueryLog());

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $arr = [];
        foreach ($orders as $order) {
            $arr[] = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_number_slug' => $order->order_number_slug,
                'warehouse' => $order->warehouse->name,
                'supplier' => $order->supplier->company,
                'ordered_at' => $order->display_ordered_at,
                'due_at' => $order->display_due_at,
                //"amount_inr" => (isset($order->amount_inr)) ? trans('app.currency_symbol_inr')." ".$order->amount_inr : "",
                'amount_inr' => (isset($order->amount_inr)) ? $fmt->formatCurrency($order->amount_inr, 'INR') : '',
                'status' => $order->display_status,
                'user' => $order->user->display_name,
            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordswithFilter,
            'data' => $arr,
            'error' => null,
        ];

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->can('edit purchase orders')) {
            $order = new PurchaseOrder;

            $order_number_count = \Setting::get('purchase_order.order_number') + 1;
            $order_number = \Setting::get('purchase_order.prefix').$order_number_count.\Setting::get('purchase_order.suffix');

            return view('purchase_orders.form', ['purchase_order' => $order, 'order_number' => $order_number, 'order_number_count' => $order_number_count]);
        }

        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseOrderRequest $request)
    {
        $validatedData = $request->validated();
        $order = PurchaseOrder::create($validatedData);
        if ($order) {

            $order_number_count = \Setting::get('purchase_order.order_number') + 1;

            \Setting::set('purchase_order.order_number', $order_number_count);
            \Setting::save();

            activity()
                ->performedOn($order)
                ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
                ->log('Created Purchase Order');

            return redirect(route('purchase-orders.cart', $order->order_number_slug));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'purchase order']));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $order_number
     * @return \Illuminate\Http\Response
     */
    public function cart($order_number_slug)
    {
        $order = PurchaseOrder::with(['supplier', 'warehouse', 'items', 'items.product', 'items.product.tax'])->where('order_number_slug', '=', $order_number_slug)->first();
        if ($order) {
            if ($order->status == PurchaseOrder::DRAFT || $order->status == PurchaseOrder::ORDERED) {
                return view('purchase_orders.cart', ['purchase_order' => $order]);
            }

            return redirect(route('purchase-orders.show', $order->order_number_slug));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  string  $order_number
     * @return \Illuminate\Http\Response
     */
    public function show($order_number_slug)
    {
        $user = Auth::user();
        if ($user->can('view purchase orders')) {
            $purchase_order = PurchaseOrder::with(['supplier', 'warehouse', 'items', 'items.product'])->where('order_number_slug', '=', $order_number_slug)->first();
            $invoices = $purchase_order->invoices->pluck('id');
            $shipped = [];

            $activities = Activity::where('subject_id', $purchase_order->id)
                ->where('subject_type', \App\Models\PurchaseOrder::class)
                ->orderBy('updated_at', 'desc')
                ->get();

            if (! empty($invoices)) {
                $shipped = PurchaseOrderInvoiceItem::groupBy('product_id')->whereIn('purchase_order_invoice_id', $invoices)
                    ->selectRaw('sum(quantity_shipped) as total_quantity_shipped, product_id')
                    ->pluck('total_quantity_shipped', 'product_id');
            }
            if ($purchase_order) {
                return view('purchase_orders.show', ['purchase_order' => $purchase_order, 'shipped' => $shipped, 'activities' => $activities]);
            }

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'purchase order']));
        }

        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        if ($request->get('field') == 'order_number') {
            $hasOrderNumber = PurchaseOrder::where('order_number', 'LIKE', $request->get('order_number'))->count();
            if ($hasOrderNumber) {
                return response()->json([
                    'success' => 'false',
                    'errors' => 'This order number is already used by another order',
                ], 409);
            }
            $order = PurchaseOrder::find($id);
            $order->order_number = $request->get('order_number');

            activity()
                ->performedOn($order)
                ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
                ->log('Order Number updated to '.$order->order_number);

            $order->update();

            return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'field' => $request->get('field')]);
        }

        if ($request->get('field') == 'warehouse') {
            $order = PurchaseOrder::find($id);
            $order->warehouse_id = $request->get('warehouse_id');

            $order->update();

            $warehouse = Warehouse::find($request->get('warehouse_id'));

            activity()
                ->performedOn($order)
                ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
                ->log('Warehouse updated to '.$warehouse->name);

            return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'field' => $request->get('field'), 'warehouse' => $warehouse]);
        }

        if ($request->get('field') == 'amount') {
            $order = PurchaseOrder::find($id);
            $items = PurchaseOrderItem::where('purchase_order_id', '=', $id)->get();
            $order->amount_usd = 0;
            foreach ($items as $item) {
                $order->amount_usd += $item->total_price;
            }
            $order->update();

            return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'field' => $request->get('field')]);
        }

        if ($request->get('field') == 'total') {
            $order = PurchaseOrder::find($id);
            $items = PurchaseOrderItem::where('purchase_order_id', '=', $id)->get();
            $order->amount_usd = 0;
            foreach ($items as $item) {
                $order->amount_usd += $item->total_price;
            }
            $order->amount_inr = $order->amount_usd * $order->order_exchange_rate;
            $order->update();

            return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'field' => $request->get('field'), 'total_usd' => $order->amount_usd, 'total_inr' => $order->amount_inr]);
        }
    }

    /**
     * Update the ordered_at and status of an order
     */
    public function ordered(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'ordered_at' => 'required|date',
        ]);

        $order = PurchaseOrder::find($id);
        $order->ordered_at = $request->ordered_at;
        $order->status = PurchaseOrder::ORDERED;
        if ($request->order_exchange_rate) {
            $order->order_exchange_rate = $request->order_exchange_rate;
        } else {
            $order->order_exchange_rate = \Setting::get('purchase_order.exchange_rate');
        }

        $items = PurchaseOrderItem::where('purchase_order_id', '=', $id)->select('quantity_confirmed', 'buying_price', 'tax')->get();
        $order->amount_usd = 0;
        foreach ($items as $item) {
            $order->amount_usd += $item->total_price;
        }
        $order->amount_inr = $order->amount_usd * $order->order_exchange_rate;

        activity()
            ->performedOn($order)
            ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
            ->log('Status updated to <b>Ordered</b>');

        $order->update();

        return redirect(route('purchase-orders.cart', $order->order_number_slug))->with('success', 'order placed');
    }

    /**
     * Update the confirmed_at and status of an order
     */
    public function confirmed(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'confirmed_at' => 'required|date',
        ]);

        $order = PurchaseOrder::find($id);
        $order->confirmed_at = $request->confirmed_at;
        $order->status = PurchaseOrder::CONFIRMED;
        if ($request->order_exchange_rate) {
            $order->order_exchange_rate = $request->order_exchange_rate;
        }

        $items = PurchaseOrderItem::where('purchase_order_id', '=', $id)->select('quantity_confirmed', 'buying_price', 'tax')->get();
        $order->amount_usd = 0;
        foreach ($items as $item) {
            $order->amount_usd += $item->total_price;
        }
        $order->amount_inr = $order->amount_usd * $order->order_exchange_rate;

        activity()
            ->performedOn($order)
            ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
            ->log('Status updated to <b>Confirmed</b>');

        $order->update();

        $inventory = new Inventory;
        $inventory->updateOrderedStock($order);

        return redirect(route('purchase-orders.show', $order->order_number_slug))->with('success', 'order confirmed');
    }

    /**
     * Update the shipped_at and status of an order
     */
    public function shipped(Request $request, int $id): RedirectResponse
    {
        // $validated = $request->validate([
        //     'shipped_at' => 'required|date',
        //     'due_at' => 'required|date',
        //     'tracking_number' => 'required',
        //     'courier' => 'required'
        // ]);
        // $order = PurchaseOrder::find($id);
        // $order->shipped_at = $request->get('shipped_at');
        // $order->due_at = $request->get('due_at');
        // $order->tracking_number = $request->get('tracking_number');
        // $order->courier = $request->get('courier');
        // $order->status = PurchaseOrder::SHIPPED;
        // $order->update();
        // return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order shipped');
    }

    /**
     * Update the shipped_at and status of an order
     */
    public function customs(Request $request, int $id): RedirectResponse
    {
        // $validated = $request->validate([
        //     'customs_at' => 'required|date',
        //     'boe_number' => 'required',
        // ]);
        // $order = PurchaseOrder::find($id);
        // $order->customs_at = $request->get('customs_at');
        // $order->boe_number = $request->get('boe_number');
        // $order->status = PurchaseOrder::CUSTOMS;
        // $order->update();
        // return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order at customs');
    }

    /**
     * Update the cleared_at and status of an order
     */
    public function cleared(Request $request, int $id): RedirectResponse
    {
        // $validated = $request->validate([
        //     'cleared_at' => 'required|date',
        //     'customs_exchange_rate' => 'required',

        // ]);
        // $order = PurchaseOrder::find($id);
        // $order->cleared_at = $request->get('cleared_at');
        // $order->customs_exchange_rate = $request->get('customs_exchange_rate');
        // $order->customs_duty = $order->amount_inr_customs * \Setting::get('purchase_order.customs_duty') / 100;
        // $order->social_welfare_surcharge = $order->customs_duty * \Setting::get('purchase_order.social_welfare_surcharge') / 100;
        // $order->igst = ($order->amount_inr + $order->customs_duty + $order->social_welfare_surcharge )* \Setting::get('purchase_order.igst') / 100;
        // $order->bank_and_transport_charges = $order->amount_inr * \Setting::get('purchase_order.transport') / 100;
        // $charges = [
        //     'customs_duty'=> \Setting::get('purchase_order.customs_duty'),
        //     'social_welfare_surcharge'=> \Setting::get('purchase_order.social_welfare_surcharge'),
        //     'igst'=> \Setting::get('purchase_order.igst'),
        //     'transport'=> \Setting::get('purchase_order.transport'),
        // ];
        // $order->charges = json_encode($charges);
        // $order->status = PurchaseOrder::CLEARED;
        // $order->update();
        // return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order cleared');
    }

    /**
     * Update the shipped_at and status of an order
     */
    public function received(Request $request, int $id): RedirectResponse
    {
        $order = PurchaseOrder::find($id);
        $order->received_at = $request->get('received_at');
        $order->status = PurchaseOrder::RECEIVED;

        activity()
            ->performedOn($order)
            ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
            ->log('Status updated to <b>Received</b>');

        $order->update();

        // $inventory = new Inventory();
        // $inventory->updateStock($order);

        return redirect(route('purchase-orders.show', $order->order_number_slug))->with('success', 'order received');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        if ($user->can('delete purchase orders')) {
            $order = PurchaseOrder::find($id);

            activity()
                ->performedOn($order)
                ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
                ->log('Purchase Order deleted');

            $order->items()->delete();
            $order->delete();

            return redirect(route('purchase-orders'))->with('success', trans('app.record_deleted', ['field' => 'Purchase Order']));
        }

        return abort(403, trans('error.unauthorized'));

    }

    public function invoice($order_number): View
    {
        $order = PurchaseOrder::where('order_number', '=', $order_number)->first();

        return view('purchase_orders.view_invoice', ['order' => $order]);
    }

    public function exportInvoiceToPdf($order_number)
    {
        $order = PurchaseOrder::where('order_number', '=', $order_number)->first();
        view()->share('order', $order);
        $pdf = PDF::loadView('purchase_orders.invoice_template', $order);

        // download PDF file with download method
        return $pdf->download($order_number.'.pdf');
    }
}
