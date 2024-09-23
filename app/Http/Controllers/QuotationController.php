<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuotationRequest;
use App\Models\Quotation;
use App\Models\QuotationItems;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;
use Spatie\Activitylog\Models\Activity;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = Quotation::getStatusList();

        return view('quotations.index', ['status' => $status]);
    }

    public function getListForDatatables(Request $request)
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

        $order_column = 'quotation_number';
        $order_dir = 'ASC';
        $order_arr = [];
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];

            switch ($column_index) {
                case 1:
                    $order_column = 'quotations.warehouse_id';
                    break;
                case 2:
                    $order_column = 'dealers.company';
                    break;
                case 3:
                    $order_column = 'states.name';
                    break;
                case 9:
                    $order_column = 'users.name';
                    break;
                default:
                    $order_column = $column_arr[$column_index]['data'];

                    $order_dir = $order_arr[0]['dir'];
            }
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Quotation::count();

        $query = Quotation::query();
        $query->join('dealers', 'dealers.id', '=', 'dealer_id');
        $query->join('warehouses', 'warehouses.id', '=', 'warehouse_id');
        $query->join('users', 'users.id', '=', 'user_id');
        $query->join('states', 'states.id', '=', 'dealers.state_id');

        if ($request->has('filter_warehouse_id')) {
            $query->where('quotations.warehouse_id', '=', $request->filter_warehouse_id);
        }

        if (! empty($column_arr[0]['search']['value'])) {
            $query->where('quotations.quotation_number', 'like', '%'.$column_arr[0]['search']['value'].'%');
        }
        if (! empty($column_arr[1]['search']['value'])) {
            $query->where('warehouses.name', 'like', '%'.$column_arr[1]['search']['value'].'%');
        }
        if (! empty($column_arr[2]['search']['value'])) {
            $query->where('dealers.company', 'like', '%'.$column_arr[2]['search']['value'].'%');
        }
        if (! empty($column_arr[3]['search']['value'])) {
            $query->where('states.name', 'like', '%'.$column_arr[3]['search']['value'].'%');
        }
        if (! empty($column_arr[4]['search']['value'])) {
            $query->where('quotations.blocked_at', 'like', convertDateToMysql($column_arr[4]['search']['value']));
        }
        if (! empty($column_arr[5]['search']['value'])) {
            $query->where('quotations.due_at', 'like', convertDateToMysql($column_arr[5]['search']['value']));
        }
        if (! empty($column_arr[6]['search']['value'])) {
            $query->where('quotations.amount', 'like', $column_arr[6]['search']['value'].'%');
        }
        if (! empty($column_arr[7]['search']['value']) && $column_arr[7]['search']['value'] != 'all') {
            $query->where('quotations.status', 'like', $column_arr[7]['search']['value']);
        }
        if (! empty($column_arr[8]['search']['value'])) {
            $query->where('users.name', 'like', $column_arr[8]['search']['value'].'%');
        }

        if ($request->has('search')) {
            $search = $request->get('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('quotations.quotation_number', 'like', '%'.$search.'%')
                    ->orWhere('quotations.amount', 'like', $search.'%')
                    ->orWhere('dealers.company', 'like', '%'.$search.'%')
                    ->orWhere('users.name', 'like', '%'.$search.'%')
                    ->orWhere('states.name', 'like', '%'.$search.'%');
            });
        }

        if ($request->has('filter_column')) {
            $filter_column = $request->get('filter_column');
            $filter_from = $request->get('filter_from');
            $filter_to = $request->get('filter_to');

            if ((! is_null($filter_from)) && (! is_null($filter_to))) {
                $filter_from = Carbon::createFromFormat('Y-m-d', $filter_from)->toDateString();
                $filter_to = Carbon::createFromFormat('Y-m-d', $filter_to)->toDateString();

                if ($filter_column == 'confirmed') {
                    $query->whereBetween('quotations.confirmed_at', [$filter_from, $filter_to]);
                } else {
                    $query->whereBetween('quotations.created_at', [$filter_from, $filter_to]);
                }
            }
        }

        $totalRecordswithFilter = $query->count();

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $query->orderBy($order_column, $order_dir);
        //$sql = $query->toSql();dd($sql);
        $orders = $query->get(['quotations.*', 'warehouses.name', 'dealers.company', 'users.name']);

        $arr = [];
        foreach ($orders as $order) {
            $total_amount = '';
            if (isset($order->amount)) {
                $curOrder = Quotation::find($order->id);
                $curOrder->calculateTotals();
                $total_amount = $curOrder->total;
            }

            $arr[] = [
                'id' => $order->id,
                'quotation_number' => $order->quotation_number,
                'quotation_number_slug' => $order->quotation_number_slug,
                'warehouse' => $order->warehouse->name,
                'dealer' => (isset($order->dealer) ? $order->dealer->company : ''),
                'state' => (isset($order->dealer->state) ? $order->dealer->state->name : ''),
                'created_at' => $order->display_created_at,
                'confirmed_at' => $order->display_confirmed_at,
                'amount' => $total_amount,
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
        // $user = Auth::user();
        // if ($user->can('edit sale orders')){

        $quote = new Quotation;

        $quote_number_count = \Setting::get('quotation.quotation_number') + 1;
        $quote_number = \Setting::get('quotation.prefix').$quote_number_count.\Setting::get('quotation.suffix');

        return view('quotations.form', ['quote' => $quote, 'quotation_number' => $quote_number, 'quotation_number_count' => $quote_number_count]);
        // }
        // return abort(403, trans('error.unauthorized'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuotationRequest $request)
    {
        $validatedData = $request->validated();

        $quote = Quotation::create($validatedData);

        if ($quote) {

            $quote->payment_terms = \Setting::get('quotation.terms');
            $quote->update();

            $quote_number_count = \Setting::get('quotation.quotation_number') + 1;
            \Setting::set('quotation.quotation_number', $quote_number_count);
            \Setting::save();

            activity()
                ->performedOn($quote)
                ->withProperties(['quotation_number' => $quote->quotation_number, 'status' => $quote->status])
                ->log('Created Quotation');

            return redirect(route('quotations.show', $quote->quotation_number_slug));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'quotation']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function show($quotation_number_slug)
    {
        //$user = Auth::user();
        //if ($user->can('view sale orders')){
        $quote = Quotation::where('quotation_number_slug', '=', $quotation_number_slug)->first();
        $quote->calculateTotals();

        $activities = Activity::where('subject_id', $quote->id)
            //->where('properties->order_number', $order->order_number)
            ->where('subject_type', 'App\Models\Quotation')
            //->where('subject_type', 'App\Models\SaleOrderPayment')
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($quote) {
            return view('quotations.show', ['order' => $quote, 'activities' => $activities, 'grand_total' => $quote->total_without_transport]);
        }

        return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'quotation']));
        //}
        //return abort(403, trans('error.unauthorized'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(quotation $quotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->get('field') == 'transport_charges') {
            $order = Quotation::find($id);
            $items = QuotationItems::where('quotation_id', '=', $id)->get();
            $order->transport_charges = $request->get('value');

            activity()
                ->performedOn($order)
                ->withProperties(['quotation_number' => $order->quotation_number, 'status' => $order->status])
                ->log('Transport Charges updated to '.number_format($order->transport_charges, 2));

            $order->update();

            $order->calculateTotals();

            return response()->json(['success' => 'true', 'total' => $order->total, 'tax_total' => $order->tax_total, 'freight_charges' => $order->freight_charges, 'transport_charges' => $order->transport_total, 'code' => 200, 'message' => 'OK', 'field' => $request->get('field')]);
        }

        if (($request->get('field') == 'amount') || ($request->get('field') == 'quantity')) {
            $order = Quotation::find($id);
            $items = QuotationItems::where('quotation_id', '=', $id)->get();

            /*
                set transport_charges to calculated field 'freight_charges'
                and amount to calculated field total_unfmt
            */
            $order->calculateTotals();
            $order->transport_charges = $order->freight_charges;
            $order->amount = $order->total_unfmt;

            $order->update();

            return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'field' => $request->get('field'), 'freight_charges' => $order->freight_charges, 'transport_charges' => $order->transport_total, 'total_cost' => $order->total_unfmt]);
        }

        if ($request->get('field') == 'payment_terms') {
            $order = Quotation::find($id);
            $order->payment_terms = $request->get('value');
            $order->update();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $order = Quotation::find($id);

        activity()
            ->performedOn($order)
            ->withProperties(['order_number' => $order->quotation_number, 'status' => $order->status])
            ->log('Quotation deleted');

        $order->items()->delete();
        $order->delete();

        if ($request->ajax()) {
            return response()->json(['deleted successfully '.$order->quotation_number_slug]);
        } else {
            return redirect(route('quotations'))->with('success', trans('app.record_deleted', ['field' => 'Quotation']));
        }
    }

    /**
     * Update the pending_at and status of a quotation
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pending(Request $request, $id)
    {
        $order = Quotation::find($id);
        $order->pending_at = $request->get('pending_at');
        $order->status = Quotation::PENDING;
        $order->update();

        activity()
            ->performedOn($order)
            ->withProperties(['quotation_number' => $order->quotation_number, 'status' => $order->status])
            ->log('Status updated to <b>Pending</b>');

        return redirect(route('quotations.show', $order->quotation_number_slug))->with('success', 'Quotation set pending - awaiting client confirmation');
    }

    /**
     * Update the confirmed_at and status of a quotation
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirmed(Request $request, $id)
    {
        $quote = Quotation::find($id);
        $quote->confirmed_at = $request->get('confirmed_at');
        $quote->status = Quotation::CONFIRMED;
        $quote->update();

        /**
         * create a PI based on this quotation
         */
        $order_number_count = \Setting::get('sale_order.order_number') + 1;
        $order_number = \Setting::get('sale_order.prefix').$order_number_count.\Setting::get('sale_order.suffix');
        $order_number_slug = str_replace([' ', '/'], '-', $order_number);

        $order = SaleOrder::create([
            'order_number' => $order_number,
            'order_number_slug' => $order_number_slug,
            'dealer_id' => $quote->dealer_id,
            'warehouse_id' => $quote->warehouse_id,
            'status' => SaleOrder::DRAFT,
            'user_id' => $quote->user_id,
            'amount' => $quote->amount,
            'transport_charges' => $quote->transport_charges,
            'payment_terms' => \Setting::get('sale_order.terms'),
        ]);

        /**
         * create PI items
         */
        foreach ($quote->items as $item) {
            $item = SaleOrderItem::create([
                'sale_order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity_ordered' => $item->quantity,
                'selling_price' => $item->price,
                'tax' => $item->tax,
            ]);
        }

        \Setting::set('sale_order.order_number', $order_number_count);
        \Setting::save();

        activity()
            ->performedOn($quote)
            ->withProperties(['quotation_number' => $quote->quotation_number, 'status' => $quote->status])
            ->log('<b>Confirmed</b> as PI '.$order_number);

        return redirect(route('quotations.show', $quote->quotation_number_slug))->with('success', 'Quotation confirmed');
    }

    public function proforma($quotation_number_slug)
    {
        $settings = \Setting::all();

        $order = Quotation::where('quotation_number_slug', '=', $quotation_number_slug)->first();
        $order->calculateTotals();

        return view('quotations.view_proforma', ['order' => $order, 'settings' => $settings, 'not_pdf' => true]);
    }

    public function exportProformaToPdf($quotation_number_slug)
    {
        $settings = \Setting::all();

        $order = Quotation::where('quotation_number_slug', '=', $quotation_number_slug)->first();
        $order->calculateTotals();
        view()->share('order', $order);
        view()->share('settings', $settings);
        $pdf = PDF::loadView('quotations.proforma', ['order' => $order]);

        // download PDF file with download method
        return $pdf->download($quotation_number_slug.' - '.$order->dealer->company.'.pdf');
    }
}
