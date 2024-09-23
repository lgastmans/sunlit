<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Requests\StoreDealerRequest;
use App\Models\Dealer;
use App\Models\SaleOrder;
use App\Models\SaleOrderPayment;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list dealers')) {
            return view('dealers.index');
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

        $order_column = 'company';
        $order_dir = 'ASC';
        $order_arr = [];
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            $order_column = $column_arr[$column_index]['data'];
            if ($column_index == 3) {
                $order_column = 'states.name';
            }

            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Dealer::count();
        $totalRecordswithFilter = Dealer::where('contact_person', 'like', '%'.$search.'%')
            ->join('states', 'states.id', '=', 'dealers.state_id')
            ->orWhere('company', 'like', '%'.$search.'%')
            ->orWhere('city', 'like', '%'.$search.'%')
            ->orWhere('states.name', 'like', $search.'%')
            ->count();

        // Fetch records
        if ($length < 0) {
            $dealers = Dealer::select('dealers.id AS dealer_id', 'company', 'contact_person', 'city', 'states.name as state_name', 'email', 'email2', 'email3', 'phone', 'phone2')
                ->where('contact_person', 'like', '%'.$search.'%')
                ->join('states', 'states.id', '=', 'dealers.state_id')
                ->orWhere('company', 'like', '%'.$search.'%')
                ->orWhere('city', 'like', '%'.$search.'%')
                ->orWhere('states.name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        } else {
            $dealers = Dealer::select('dealers.id AS dealer_id', 'company', 'contact_person', 'city', 'states.name as state_name', 'email', 'email2', 'email3', 'phone', 'phone2')
                ->where('contact_person', 'like', '%'.$search.'%')
                ->join('states', 'states.id', '=', 'state_id')
                ->orWhere('company', 'like', '%'.$search.'%')
                ->orWhere('city', 'like', '%'.$search.'%')
                ->orWhere('states.name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();
        }

        $arr = [];

        foreach ($dealers as $record) {

            $arr[] = [
                'id' => $record->dealer_id,
                'company' => $record->company,
                'contact_person' => $record->contact_person,
                'city' => $record->city,
                'state_name' => $record->state_name,
                'email' => $record->email.(! empty($record->email2) ? ', '.$record->email2 : '').(! empty($record->email3) ? ', '.$record->email3 : ''),
                'phone' => $record->phone.(! empty($record->phone2) ? ', '.$record->phone2 : ''),
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

    public function getListForLedger(Request $request)
    {
        $vdata = [];

        $ret = [
            'data' => [],
            'footer' => ['debit_total' => 0, 'credit_total' => 0],
            'customer' => ['company' => '', 'address' => ''],
        ];

        $dealer_id = 0;
        if ($request->has('dealer_id')) {
            $dealer_id = $request->get('dealer_id');
        }

        $select_period = 'period_monthly';
        if ($request->has('select_period')) {
            $select_period = $request->get('select_period');
        }

        $month = date('n');
        if ($request->has('month_id')) {
            $month = $request->get('month_id');
        }

        $year = date('Y');
        if ($request->has('year_id')) {
            $year = $request->get('year_id');
        }

        $quarter = '';
        if ($request->has('quarterly_id')) {
            $quarter = $request->get('quarterly_id');
        }

        /**
         * get the dealer's invoices
         */
        $query = SaleOrder::query();
        $query->select('sale_orders.id', 'sale_orders.dispatched_at', 'sale_orders.order_number_slug', 'sale_orders.amount', 'sale_orders.transport_charges');
        $query->where('sale_orders.status', '>=', '4');
        $query->where('sale_orders.dealer_id', '=', $dealer_id);

        if ($select_period == 'period_monthly') {
            $query->whereYear('sale_orders.dispatched_at', '=', $year);
            $query->whereMonth('sale_orders.dispatched_at', '=', $month);
        } elseif ($select_period == 'period_quarterly') {
            if ($quarter == 'Q1') {
                $from = date($year.'-01-01');
                $to = date($year.'-03-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            } elseif ($quarter == 'Q2') {
                $from = date($year.'-04-01');
                $to = date($year.'-06-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            } elseif ($quarter == 'Q3') {
                $from = date($year.'-07-01');
                $to = date($year.'-09-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            } elseif ($quarter == 'Q4') {
                $from = date($year.'-10-01');
                $to = date($year.'-12-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
        } elseif ($select_period == 'period_yearly') {
            $query->whereYear('sale_orders.dispatched_at', '=', $year);
        }

        $query->orderBy('sale_orders.dispatched_at', 'ASC');
        $query->orderBy('sale_orders.order_number_slug', 'ASC');
        //dd($query->toSql());
        $rows = $query->get();

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        $arr = [];
        $footer = ['label' => 'Totals', 'total_quantity' => 0, 'total_taxable_value' => 0, 'total_tax_amount' => 0, 'total_amount' => 0];

        $debit_total = 0;
        $credit_total = 0;

        foreach ($rows as $obj) {
            $row = [];

            $curOrder = SaleOrder::find($obj->id);
            $curOrder->calculateTotals();
            if ($curOrder) {
                $total = $curOrder->total_unfmt;
            }

            $row['vch_date'] = date('d-m-Y', strtotime($obj->dispatched_at));
            $row['particulars'] = $obj->order_number_slug;
            $row['vch_type'] = 'Sales';
            $row['vch_no'] = $obj->order_number_slug;
            $row['debit'] = $fmt->formatCurrency($total, 'INR');
            $row['credit'] = '';

            $vdata[] = $row;

            $debit_total += $total;

            /**
             * get the payments against the invoice
             */
            $sale_order = SaleOrder::find($obj->id);

            foreach ($sale_order->sale_order_payments as $payment) {
                $row['vch_date'] = date('d-m-Y', strtotime($payment->paid_at));
                $row['particulars'] = $payment->reference;
                $row['vch_type'] = 'Receipt';
                $row['vch_no'] = $obj->order_number_slug;
                $row['debit'] = '';
                $row['credit'] = $fmt->formatCurrency($payment->amount, 'INR');

                $vdata[] = $row;

                $credit_total += $payment->amount;
            }
        }

        /**
         * sort the data by date
         */
        usort($vdata, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d-m-Y', $a['vch_date']);
            $dateB = DateTime::createFromFormat('d-m-Y', $b['vch_date']);

            return $dateA >= $dateB;
        });

        $balance_total = $debit_total - $credit_total;
        $balance_total = $fmt->formatCurrency($balance_total, 'INR');
        $debit_total = $fmt->formatCurrency($debit_total, 'INR');
        $credit_total = $fmt->formatCurrency($credit_total, 'INR');

        $response = [
            //"draw" => $draw,
            //"recordsTotal" => $totalRecords,
            //"recordsFiltered" => $totalRecordswithFilter,
            'data' => [
                'data' => $vdata,
                'footer' => ['debit_total' => $debit_total, 'credit_total' => $credit_total, 'balance_total' => $balance_total],
            ],
            'error' => null,
        ];

        return response()->json($response);
    }

    public function getListForLedgerSummary(Request $request): JsonResponse
    {
        $select_period = 'period_monthly';
        if ($request->has('select_period')) {
            $select_period = $request->get('select_period');
        }

        $month = date('n');
        if ($request->has('month_id')) {
            $month = $request->get('month_id');
        }

        $year = date('Y');
        if ($request->has('year_id')) {
            $year = $request->get('year_id');
        }

        $quarter = '';
        if ($request->has('quarterly_id')) {
            $quarter = $request->get('quarterly_id');
        }

        $vdata = [];

        $ret = [
            'data' => [],
            'footer' => ['debit_total' => 0, 'credit_total' => 0],
            'customer' => ['company' => '', 'address' => ''],
        ];

        $serial = 1;

        $debit_grand_total = 0;
        $credit_grand_total = 0;
        $balance_grand_total = 0;

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        $column_arr = $request->get('columns');
        if (! empty($column_arr[1]['search']['value'])) {
            $query = Dealer::select('*');
            $query->where('dealers.company', 'like', '%'.$column_arr[1]['search']['value'].'%');
            $dealers = $query->get();
        } else {
            $dealers = Dealer::all();
        }

        foreach ($dealers as $dealer) {
            $row = [];

            $row['dealer_id'] = $dealer->id;
            $row['serial_number'] = $serial;
            $row['dealer'] = $dealer->company.', '.$dealer->address.' - '.$dealer->city.'-'.$dealer->zip_code;
            $row['debit_unfmt'] = 0;
            $row['debit'] = 0;
            $row['credit'] = 0;
            $row['balance'] = 0;

            $debit_total = 0;
            $credit_total = 0;
            $balance_total = 0;

            /**
             * get the invoices total
             * could not run a SUM query as the amount saved does not include taxes,
             * and transport charges
             */
            $query = SaleOrder::select('sale_orders.id');
            //$query = SaleOrder::select(DB::raw('SUM(sale_orders.amount + sale_orders.transport_charges) AS dealer_debit'));
            $query->where('sale_orders.status', '>=', '4');
            $query->where('sale_orders.dealer_id', '=', $dealer->id);

            if ($select_period == 'period_monthly') {
                $query->whereYear('sale_orders.dispatched_at', '=', $year);
                $query->whereMonth('sale_orders.dispatched_at', '=', $month);
            } elseif ($select_period == 'period_quarterly') {
                if ($quarter == 'Q1') {
                    $from = date($year.'-01-01');
                    $to = date($year.'-03-31');
                    $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
                } elseif ($quarter == 'Q2') {
                    $from = date($year.'-04-01');
                    $to = date($year.'-06-30');
                    $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
                } elseif ($quarter == 'Q3') {
                    $from = date($year.'-07-01');
                    $to = date($year.'-09-30');
                    $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
                } elseif ($quarter == 'Q4') {
                    $from = date($year.'-10-01');
                    $to = date($year.'-12-31');
                    $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
                }
            } elseif ($select_period == 'period_yearly') {
                $query->whereYear('sale_orders.dispatched_at', '=', $year);
            }

            //$query->groupBy('sale_orders.dealer_id');
            //$ret = $query->toSql(); print_r($ret);die();
            //$sale_order = $query->first();
            $sale_orders = $query->get();

            foreach ($sale_orders as $sale_order) {
                $curOrder = SaleOrder::find($sale_order->id);
                $curOrder->calculateTotals();
                if ($curOrder) {
                    $debit_total += $curOrder->total_unfmt;
                }
            }

            /**
             * get the payments total
             *
             * October 2023: Rishi requested to remove the credit and balance columns
             */
            /*
            $query = SaleOrderPayment::select(DB::raw('SUM(amount) AS dealer_credit'));
            $query->where('sale_order_payments.dealer_id', '=', $dealer->id);
            $query->groupBy('sale_order_payments.dealer_id');

            $sale_order_payment = $query->first();

            if ($sale_order_payment)
            {
                $credit_total = $sale_order_payment->dealer_credit;
            }

            $balance_total = $debit_total - $credit_total;
            */

            // if ($balance_total > 0)
            // {
            $row['debit_unfmt'] = intval($debit_total);
            $row['debit'] = $fmt->formatCurrency($debit_total, 'INR');
            $row['credit'] = 0; //$fmt->formatCurrency($credit_total, "INR");
            $row['balance'] = 0; //$fmt->formatCurrency($balance_total, "INR");

            $vdata[] = $row;
            $serial++;

            $debit_grand_total += $debit_total;
            //$credit_grand_total += $credit_total;
            //$balance_grand_total += $balance_total;
            // }

        }

        usort($vdata, fn ($a, $b) => $b['debit_unfmt'] <=> $a['debit_unfmt']);

        $debit_grand_total = $fmt->formatCurrency($debit_grand_total, 'INR');
        $credit_grand_total = 0; //$fmt->formatCurrency($credit_grand_total, "INR");
        $balance_grand_total = 0; //$fmt->formatCurrency($balance_grand_total, "INR");

        $response = [
            //"draw" => $draw,
            //"recordsTotal" => $totalRecords,
            //"recordsFiltered" => $totalRecordswithFilter,
            'data' => [
                'data' => $vdata,
                'footer' => ['label' => 'TOTALS', 'debit_total' => $debit_grand_total, 'credit_total' => $credit_grand_total, 'balance_total' => $balance_grand_total],
            ],
            'error' => null,
        ];

        return response()->json($response);
    }

    public function ledger(Request $request): View
    {
        $curQuarter = getCurrentQuarter();
        $dealer_id = null;
        if ($request->has('id')) {
            $dealer_id = $request->get('id');
        }

        return view('dealers.ledger', ['dealer_id' => $dealer_id, 'curQuarter' => $curQuarter, 'dealer_id' => 0]);
    }

    public function ledgerSummary(Request $request): View
    {
        $curQuarter = getCurrentQuarter();

        return view('dealers.ledger-summary', ['curQuarter' => $curQuarter]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $dealer = new Dealer;

        return view('dealers.form', ['dealer' => $dealer]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreDealerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDealerRequest $request)
    {
        $validatedData = $request->validated();
        $dealer = Dealer::create($validatedData);
        if ($dealer) {
            return redirect(route('dealers'))->with('success', trans('app.record_added', ['field' => 'dealers']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'dealers']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = Auth::user();
        if ($user->can('view dealers')) {
            $dealer = Dealer::with('state')->find($id);
            if ($dealer) {
                return view('dealers.show', ['dealer' => $dealer]);
            }

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'dealer']));
        }

        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id): View
    {
        $dealer = Dealer::with('state')->find($id);
        if ($dealer) {
            return view('dealers.form', ['dealer' => $dealer]);
        }

        return view('dealers.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreDealerRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreDealerRequest $request, int $id)
    {
        $validatedData = $request->validated();
        $dealer = Dealer::whereId($id)->update($validatedData);
        if ($dealer) {
            return redirect(route('dealers'))->with('success', trans('app.record_edited', ['field' => 'dealers']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'dealers']));
    }

    /**
     * Display a listing of the resource for select2
     *
     * @return json
     */
    public function getListForSelect2(Request $request): json
    {
        $query = Dealer::query();
        if ($request->has('q')) {
            $query->where('company', 'like', $request->get('q').'%');
        } elseif ($request->has('dealer_id')) {
            $dealer_id = $request->get('dealer_id');
            if (! is_null($dealer_id)) {
                $query->where('id', '=', $request->get('dealer_id'));
            }
        }
        $dealers = $query->select('id', 'company as text')->get();

        return ['results' => $dealers];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        if ($user->can('delete dealers')) {

            $dealer = Dealer::find($id);

            if ($dealer->sale_orders()->exists()) {
                return redirect(route('dealers'))->with('error', trans('app.foreign_key_constraints', ['field' => 'Invoices', 'model' => 'dealer']));
            } else {
                Dealer::destroy($id);

                return redirect(route('dealers'))->with('success', trans('app.record_deleted', ['field' => 'dealers']));
            }
        }

        return abort(403, trans('error.unauthorized'));
    }
}
