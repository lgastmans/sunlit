<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseOrderInvoiceRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderInvoice;
use App\Models\PurchaseOrderInvoiceItem;
use App\Models\PurchaseOrderItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PDF;
use Spatie\Activitylog\Models\Activity;

class PurchaseOrderInvoiceController extends Controller
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
            $status = PurchaseOrderInvoice::getStatusList();

            return view('purchase_order_invoices.index', ['status' => $status]);

        }

        return abort(403, trans('error.unauthorized'));
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

        $order_column = 'invoice_number';
        $order_dir = 'ASC';
        $order_arr = [];
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

        $totalRecords = PurchaseOrderInvoice::count();

        $query = PurchaseOrderInvoice::query();
        $query->join('users', 'users.id', '=', 'user_id');
        $query->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_id');
        $query->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id');

        if (! empty($column_arr[0]['search']['value'])) {
            $query->where('purchase_order_invoices.invoice_number', 'like', '%'.$column_arr[0]['search']['value'].'%');
        }
        if (! empty($column_arr[1]['search']['value'])) {
            $query->where('purchase_orders.order_number', 'like', '%'.$column_arr[1]['search']['value'].'%');
        }
        if (! empty($column_arr[2]['search']['value'])) {
            $query->where('suppliers.company', 'like', '%'.$column_arr[2]['search']['value'].'%');
        }
        if (! empty($column_arr[3]['search']['value'])) {
            $query->where('purchase_order_invoices.shipped_at', 'like', convertDateToMysql($column_arr[3]['search']['value']));
        }
        if (! empty($column_arr[4]['search']['value'])) {
            $query->where('purchase_order_invoices.amount_inr', 'like', $column_arr[4]['search']['value'].'%');
        }
        if (! empty($column_arr[5]['search']['value']) && $column_arr[5]['search']['value'] != 'all') {
            $query->where('purchase_order_invoices.status', 'like', $column_arr[5]['search']['value']);
        }
        if (! empty($column_arr[6]['search']['value'])) {
            $query->where('users.name', 'like', $column_arr[6]['search']['value'].'%');
        }

        if ($request->has('search')) {
            $search = $request->get('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('purchase_orders.order_number', 'like', '%'.$search.'%')
                    ->orWhere('purchase_order_invoices.amount_inr', 'like', $search.'%')
                    ->orWhere('purchase_order_invoices.invoice_number', 'like', '%'.$search.'%')
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
                //$filter_from = Carbon::parse('Y-m-d', $filter_from)->toDateString();
                //$filter_to = Carbon::parse('Y-m-d', $filter_to)->toDateString();

                if ($filter_column == 'shipped') {
                    $query->whereBetween('purchase_order_invoices.shipped_at', [$filter_from, $filter_to]);
                }
            }
        }

        $totalRecordswithFilter = $query->count();

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $query->orderBy($order_column, $order_dir);
        $invoices = $query->select('purchase_order_invoices.*', 'purchase_orders.order_number', 'suppliers.company')->get();

        $arr = [];
        foreach ($invoices as $invoice) {
            $arr[] = [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_number_slug' => $invoice->invoice_number_slug,
                'order_number' => $invoice->purchase_order->order_number,
                'supplier' => $invoice->company,
                'shipped_at' => $invoice->display_shipped_at,
                'amount' => (isset($invoice->amount_inr)) ? trans('app.currency_symbol_inr').' '.$invoice->amount_inr : '',
                'status' => $invoice->display_status,
                'user' => $invoice->user->display_name,
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
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseOrderInvoiceRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $purchase_order = PurchaseOrder::find($request->purchase_order_id);
        /*
        $purchase_order->status = PurchaseOrder::SHIPPED;
        $purchase_order->save();
        */

        $invoice = new PurchaseOrderInvoice;
        $invoice->purchase_order_id = $purchase_order->id;
        $invoice->order_exchange_rate = $purchase_order->order_exchange_rate;
        $invoice->status = PurchaseOrderInvoice::SHIPPED;
        $invoice->invoice_number = $validatedData['invoice_number'];
        $invoice->shipped_at = $validatedData['shipped_at'];
        $invoice->user_id = $validatedData['user_id'];
        $invoice->invoice_number_slug = $validatedData['invoice_number_slug'];
        $invoice->payment_terms = \Setting::get('purchase_order.terms');
        $invoice->save();

        activity()
            ->performedOn($invoice)
            ->withProperties(['order_number' => $invoice->invoice_number, 'status' => $invoice->status])
            ->log('Created Purchase Order Invoice');

        $invoice_id = $invoice->id;
        $invoice_number = $invoice->invoice_number;
        $invoice_number_slug = $invoice->invoice_number_slug;

        $amount_usd = 0;
        foreach ($request->products as $product_id => $quantity_shipped) {
            $product = Product::find($product_id);
            $invoice_item = new PurchaseOrderInvoiceItem;
            $invoice_item->purchase_order_invoice_id = $invoice_id;
            $invoice_item->product_id = $product_id;
            $invoice_item->quantity_shipped = $quantity_shipped;
            $purchase_order_item = PurchaseOrderItem::where('purchase_order_id', $purchase_order->id)->where('product_id', $product_id)->first();

            /**
             * purchase_order_item->buying_price is either INR or USD based on the supplier
             * being domestic (India) or International
             */
            $invoice_item->buying_price = $purchase_order_item->buying_price;

            if ($purchase_order->supplier->is_international) {
                $invoice_item->customs_duty = $invoice_item->buying_price * $invoice_item->quantity_shipped * ($product->category->customs_duty / 100);

                $invoice_item->social_welfare_surcharge = $invoice_item->customs_duty * ($product->category->social_welfare_surcharge / 100);

                $invoice_item->igst = (($invoice_item->buying_price * $quantity_shipped) + $invoice_item->customs_duty + $invoice_item->social_welfare_surcharge) * ($product->category->igst / 100);

                $charges = [
                    'customs_duty' => $product->category->customs_duty,
                    'social_welfare_surcharge' => $product->category->social_welfare_surcharge,
                    'igst' => $product->category->igst,
                ];
            } else {
                $invoice_item->customs_duty = 0;
                $invoice_item->social_welfare_surcharge = 0;
                $invoice_item->igst = 0;
                $charges = [
                    'customs_duty' => 0,
                    'social_welfare_surcharge' => 0,
                    'igst' => 0,
                ];
            }

            $invoice_item->charges = $charges;

            $invoice_item->save();

            $amount_usd += $invoice_item->quantity_shipped * $invoice_item->buying_price;
        }

        $inventory = new Inventory;
        $inventory->updateStock($invoice);

        if ($purchase_order->supplier->is_international) {
            $invoice->amount_usd = $amount_usd;
            $invoice->amount_inr = $invoice->amount_usd * $invoice->order_exchange_rate;
        } else {
            $invoice->amount_usd = 0;
            $invoice->amount_inr = $amount_usd;
        }
        $invoice->update();

        return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'redirect' => route('purchase-order-invoices.show', $invoice_number_slug)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_number_slug)
    {
        $user = Auth::user();
        if ($user->can('view purchase orders')) {
            $po = PurchaseOrderInvoice::with('purchase_order')->where('invoice_number_slug', '=', $invoice_number_slug)->first();
            $purchase_order = $po->purchase_order;
            $invoice = PurchaseOrderInvoice::with(['items', 'items.product'])->where('invoice_number_slug', '=', $invoice_number_slug)->first();

            $activities = Activity::with(['causer' => function ($query) {
                    $query->withTrashed();
                }])
                ->where('subject_id', $po->id)
                ->where('subject_type', \App\Models\PurchaseOrderInvoice::class)
                ->orderBy('updated_at', 'desc')
                ->get();

            if ($invoice) {
                return view('purchase_order_invoices.show', ['invoice' => $invoice, 'purchase_order' => $purchase_order, 'activities' => $activities]);
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
    public function edit(PurchaseOrderInvoice $purchaseOrderInvoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
        if ($request->get('field') == 'payment_terms') {
            $order = PurchaseOrderInvoice::find($id);
            $order->payment_terms = $request->get('value');
            $order->update();
        }
    }

    /**
     * Update the shipped_at and status of an order
     */
    public function customs(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'customs_at' => 'required|date',
            'boe_number' => 'required',
        ]);
        $invoice = PurchaseOrderInvoice::find($id);
        $invoice->customs_at = $request->get('customs_at');
        $invoice->boe_number = $request->get('boe_number');
        $invoice->status = PurchaseOrderInvoice::CUSTOMS;

        activity()
            ->performedOn($invoice)
            ->withProperties(['order_number' => $invoice->invoice_number, 'status' => $invoice->status])
            ->log('Status updated to <b>Customs</b>');

        $invoice->update();

        return redirect(route('purchase-order-invoices.show', $invoice->invoice_number_slug))->with('success', 'order at customs');
    }

    /**
     * Update the cleared_at and status of an order
     */
    public function cleared(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'cleared_at' => 'required|date',
            'customs_exchange_rate' => 'required',
        ]);
        $invoice = PurchaseOrderInvoice::find($id);
        $invoice->cleared_at = $request->get('cleared_at');
        $invoice->customs_exchange_rate = $request->get('customs_exchange_rate');

        $invoice->customs_duty = $request->get('total_customs_duty_inr');
        $invoice->social_welfare_surcharge = $request->get('total_social_welfare_surcharge_inr');
        $invoice->igst = $request->get('total_igst_inr');
        $invoice->landed_cost = $request->get('landed_cost_inr');

        $invoice->status = PurchaseOrderInvoice::CLEARED;

        activity()
            ->performedOn($invoice)
            ->withProperties(['order_number' => $invoice->invoice_number, 'status' => $invoice->status])
            ->log('Status updated to <b>Cleared</b>');

        $invoice->update();

        $invoice->updateItemsBuyingPrice($id);

        return redirect(route('purchase-order-invoices.show', $invoice->invoice_number_slug))->with('success', 'order cleared');
    }

    /**
     * Update the shipped_at and status of an order
     */
    public function received(Request $request, int $id): RedirectResponse
    {
        $invoice = PurchaseOrderInvoice::find($id);
        $invoice_number = $invoice->invoice_number;
        $invoice->received_at = $request->get('received_at');
        $invoice->status = PurchaseOrderInvoice::RECEIVED;
        $invoice->update();

        $inventory = new Inventory;

        activity()
            ->performedOn($invoice)
            ->withProperties(['order_number' => $invoice->invoice_number, 'status' => $invoice->status])
            ->log('Status updated to <b>Received</b>');

        $inventory->updateStock($invoice);

        return redirect(route('purchase-order-invoices.show', $invoice->invoice_number_slug))->with('success', 'order received');
    }

    /**
     * Update the paid_at and status of an order
     */
    public function paid(Request $request, int $id): RedirectResponse
    {

        $invoice = PurchaseOrderInvoice::with('purchase_order')->find($id);

        if ($invoice->purchase_order->supplier->is_international) {
            $validated = $request->validate([
                'paid_at' => 'required|date',
                'paid_exchange_rate' => 'required',
            ]);
        } else {
            $validated = $request->validate([
                'paid_at' => 'required|date',
            ]);
        }

        //$purchase_order = PurchaseOrder::find($invoice->purchase_order_id);

        $invoice_number = $invoice->invoice_number;
        $invoice->paid_at = $request->get('paid_at');
        if ($invoice->purchase_order->supplier->is_international) {
            $invoice->paid_exchange_rate = $request->get('paid_exchange_rate');
            $invoice->paid_cost = $invoice->amount_usd * $request->get('paid_exchange_rate');
        } else {
            $invoice->paid_exchange_rate = 1;
            $invoice->paid_cost = $invoice->amount_usd * 1;
        }
        $invoice->payment_reference = $request->get('payment_reference');
        $invoice->status = PurchaseOrderInvoice::PAID;

        activity()
            ->performedOn($invoice)
            ->withProperties(['order_number' => $invoice->invoice_number, 'status' => $invoice->status])
            ->log('Status updated to <b>Paid</b>');

        $invoice->update();

        $invoice->updateItemsPaidPrice($id);

        return redirect(route('purchase-order-invoices.show', $invoice->invoice_number_slug))->with('success', 'order received');
    }

    /**
     * Mark the specified resource as cancelled
     *
     * only if the status of the invoice is "RECEIVED" or "PAID"
     *  should the inventory be updated
     * else just mark the items as "deleted"
     *
     *
     * @param  \App\Models\PurchaseOrderInvoice  $purchaseOrderInvoice
     */
    public function cancelled(Request $request, $id): JsonResponse
    {
        $invoice = PurchaseOrderInvoice::with('items')->find($id);

        $initial_status = $invoice->status;

        $invoice->status = PurchaseOrderInvoice::CANCELLED;
        $invoice->update();

        if (($initial_status == PurchaseOrderInvoice::RECEIVED) || ($initial_status == PurchaseOrderInvoice::PAID)) {

            $inventory = new Inventory;
            $inventory->updateStock($invoice);

        } else {
            foreach ($invoice->items as $item) {
                $item->delete();
            }
        }

        activity()
            ->performedOn($invoice)
            ->withProperties(['order_number' => $invoice->invoice_number, 'status' => $invoice->status])
            ->log('Invoice <b>Cancelled</b>');

        return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'redirect' => route('purchase-order-invoices.show', $invoice->invoice_number_slug)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseOrderInvoice $purchaseOrderInvoice)
    {
        //
    }

    public function proforma($invoice_number_slug): View
    {
        $settings = \Setting::all();

        $order = PurchaseOrderInvoice::where('invoice_number_slug', '=', $invoice_number_slug)->first();
        //$order->calculateTotals();

        view()->share('order', $order);
        view()->share('settings', $settings);

        return view('purchase_order_invoices.view_proforma', ['order' => $order, 'settings' => $settings]);
    }

    public function exportProformaToPdf($invoice_number_slug)
    {
        $settings = \Setting::all();

        $order = PurchaseOrderInvoice::where('invoice_number_slug', '=', $invoice_number_slug)->first();
        //$order->calculateTotals();
        view()->share('order', $order);
        view()->share('settings', $settings);
        $pdf = PDF::loadView('purchase_order_invoices.proforma', ['order' => $order]);

        // download PDF file with download method
        return $pdf->download('Purchase Order Invoice '.$invoice_number_slug.'.pdf');
    }
}
