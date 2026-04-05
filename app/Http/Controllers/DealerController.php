<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDealerRequest;
use App\Models\Dealer;
use App\Models\SaleOrder;
use App\Models\SaleOrderPayment;
use DateTime;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
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

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        $column_arr = $request->get('columns', []);
        $dealerSearch = trim(data_get($column_arr, '1.search.value', ''));

        $orderTotalsQuery = SaleOrder::query()
            ->leftJoin('sale_order_items', function ($join) {
                $join->on('sale_order_items.sale_order_id', '=', 'sale_orders.id')
                    ->whereNull('sale_order_items.deleted_at');
            })
            ->select(
                'sale_orders.id',
                'sale_orders.dealer_id'
            )
            ->selectRaw('
                ROUND(
                    (
                        COALESCE(SUM(sale_order_items.quantity_ordered * sale_order_items.selling_price), 0)
                        + COALESCE(SUM((sale_order_items.quantity_ordered * sale_order_items.selling_price) * (sale_order_items.tax / 100)), 0)
                        + COALESCE(sale_orders.transport_charges, 0)
                        + (COALESCE(sale_orders.transport_charges, 0) * COALESCE(sale_orders.transport_tax, 0) / 100)
                    )
                    +
                    (
                        (
                            COALESCE(SUM(sale_order_items.quantity_ordered * sale_order_items.selling_price), 0)
                            + COALESCE(SUM((sale_order_items.quantity_ordered * sale_order_items.selling_price) * (sale_order_items.tax / 100)), 0)
                            + COALESCE(sale_orders.transport_charges, 0)
                            + (COALESCE(sale_orders.transport_charges, 0) * COALESCE(sale_orders.transport_tax, 0) / 100)
                        ) * COALESCE(sale_orders.tcs, 0) / 100
                    )
                ) AS order_total
            ')
            ->where('sale_orders.status', '>=', SaleOrder::DISPATCHED);

        if ($select_period == 'period_monthly') {
            $orderTotalsQuery->whereYear('sale_orders.dispatched_at', '=', $year);
            $orderTotalsQuery->whereMonth('sale_orders.dispatched_at', '=', $month);
        } elseif ($select_period == 'period_quarterly') {
            if ($quarter == 'Q1') {
                $from = $year.'-01-01';
                $to = $year.'-03-31';
                $orderTotalsQuery->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            } elseif ($quarter == 'Q2') {
                $from = $year.'-04-01';
                $to = $year.'-06-30';
                $orderTotalsQuery->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            } elseif ($quarter == 'Q3') {
                $from = $year.'-07-01';
                $to = $year.'-09-30';
                $orderTotalsQuery->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            } elseif ($quarter == 'Q4') {
                $from = $year.'-10-01';
                $to = $year.'-12-31';
                $orderTotalsQuery->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
        } elseif ($select_period == 'period_yearly') {
            $orderTotalsQuery->whereYear('sale_orders.dispatched_at', '=', $year);
        }

        $orderTotalsQuery->groupBy(
            'sale_orders.id',
            'sale_orders.dealer_id',
            'sale_orders.transport_charges',
            'sale_orders.transport_tax',
            'sale_orders.tcs'
        );

        $salesTotalsQuery = DB::query()
            ->fromSub($orderTotalsQuery, 'order_totals')
            ->select(
                'order_totals.dealer_id',
                DB::raw('SUM(order_totals.order_total) AS debit_total')
            )
            ->groupBy('order_totals.dealer_id');

        $dealers = Dealer::query()
            ->leftJoinSub($salesTotalsQuery, 'sales_totals', function ($join) {
                $join->on('sales_totals.dealer_id', '=', 'dealers.id');
            })
            ->select(
                'dealers.id',
                'dealers.company',
                'dealers.address',
                'dealers.city',
                'dealers.zip_code',
                DB::raw('COALESCE(sales_totals.debit_total, 0) AS debit_unfmt')
            )
            ->when($dealerSearch !== '', function ($query) use ($dealerSearch) {
                $query->where('dealers.company', 'like', '%'.$dealerSearch.'%');
            })
            ->orderByDesc('debit_unfmt')
            ->get();

        $debit_grand_total = (float) $dealers->sum('debit_unfmt');
        $credit_grand_total = 0;
        $balance_grand_total = 0;

        $vdata = [];
        foreach ($dealers->values() as $index => $dealer) {
            $debit = (float) $dealer->debit_unfmt;

            $vdata[] = [
                'dealer_id' => $dealer->id,
                'serial_number' => $index + 1,
                'dealer' => $dealer->company.', '.$dealer->address.' - '.$dealer->city.'-'.$dealer->zip_code,
                'debit' => $fmt->formatCurrency($debit, 'INR'),
                'debit_unfmt' => (int) $debit,
                'credit' => 0,
                'balance' => 0,
            ];
        }

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
     */
    public function create(): View
    {
        $dealer = new Dealer;

        return view('dealers.form', ['dealer' => $dealer]);
    }

    /**
     * Store a newly created resource in storage.
     *
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
     */
    public function getListForSelect2(Request $request): json | array
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
