<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleOrderPaymentRequest;
use App\Models\SaleOrder;
use App\Models\SaleOrderPayment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;

class SaleOrderPaymentController extends Controller
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

        $order_column = 'paid_at';
        $order_dir = 'ASC';
        $order_arr = [];
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            $order_column = $column_arr[$column_index]['data'];
            $order_dir = $order_arr[0]['dir'];
        }

        // $search = '';
        // if ($request->has('search')) {
        //     $search_arr = $request->get('search');
        //     $search = $search_arr['value'];
        // }

        $filter_sale_order_id = $request->get('filter_sale_order_id');

        // Total records
        $totalRecords = SaleOrderPayment::where('sale_order_id', '=', $filter_sale_order_id)->count();
        $totalRecordswithFilter = SaleOrderPayment::where('sale_order_id', '=', $filter_sale_order_id)->count();

        // Fetch records
        if ($length < 0) {
            $payments = SaleOrderPayment::select('sale_order_payments.*')
                ->where('sale_order_id', '=', $filter_sale_order_id)
                ->orderBy($order_column, $order_dir)
                ->get();
        } else {
            $payments = SaleOrderPayment::select('sale_order_payments.*')
                ->where('sale_order_id', '=', $filter_sale_order_id)
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();
        }

        $arr = [];

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        foreach ($payments as $record) {

            $dt = Carbon::parse($record->paid_at);
            // return $dt->toFormattedDateString();

            $arr[] = [
                'id' => $record->id,
                'amount' => $fmt->formatCurrency($record->amount, 'INR'),
                'payment_reference' => $record->reference,
                'payment_date' => $dt->toDateString(),
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleOrderPaymentRequest $request): JsonResponse
    {
        $log = [];
        $log_text = '';

        $order = SaleOrder::find($request->get('sale_order_id'));

        if (is_null($request->get('payment_id'))) {
            $validatedData = $request->validated();
            $payment = SaleOrderPayment::create($validatedData);

            $dt = Carbon::parse($payment->paid_at);

            $log_text = 'Payment '.$payment->reference.' added, dated '.$dt->toDateString();

            activity()
               //->performedOn($payment)
                ->performedOn($order)
                ->withProperties(['reference' => $payment->reference, 'amount' => $payment->amount, 'order_number' => $order->order_number])
                ->log($log_text);
        } else {
            $payment = SaleOrderPayment::find($request->get('payment_id'));
            if ($payment) {
                $payment->amount = $request->get('amount');
                $payment->reference = $request->get('reference');
                $payment->paid_at = $request->get('paid_at');
                $payment->save();

                $dt = Carbon::parse($payment->paid_at);

                $log_text = 'Payment '.$payment->reference.' edited, dated '.$dt->toDateString();

                activity()
                   //->performedOn($payment)
                    ->performedOn($order)
                    ->withProperties(['reference' => $payment->reference, 'amount' => $payment->amount, 'order_number' => $order->order_number])
                    ->log($log_text);
            }
        }

        $log['log_date'] = $dt->toDateString();
        $log['log_text'] = $log_text.' by <b>'.Auth::user()->name.'</b>';

        // return response()->json($validatedData);
        return response()->json($log);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
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
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $payment = SaleOrderPayment::find($id);

        $dt = Carbon::parse($payment->paid_at);

        activity()
            ->performedOn($payment)
            ->withProperties(['paid_at' => $payment->paid_at, 'reference' => $payment->reference, 'amount' => $payment->amount])
            ->log('Payment '.$payment->reference.' dated '.$dt->toDateString().' deleted');

        SaleOrderPayment::destroy($id);

        $log['log_date'] = $dt->toDateString();
        $log['log_text'] = 'Payment '.$payment->reference.' dated '.$dt->toDateString().' deleted'.' by <b>'.Auth::user()->name.'</b>';

        // return response()->json($validatedData);
        return response()->json($log);

    }

    public function updatePaymentsData()
    {
        /**
         * set the dealer_id for each payment
         */
        $sale_orders = SaleOrder::all();

        foreach ($sale_orders as $sale_order) {
            foreach ($sale_order->sale_order_payments as $payment) {
                $sale_order_payment = SaleOrderPayment::find($payment->id);
                // echo $sale_order_payment->dealer_id."<br>";
                $sale_order_payment->dealer_id = $sale_order->dealer_id;
                $sale_order_payment->save();
            }
        }

        return false;
    }
}
