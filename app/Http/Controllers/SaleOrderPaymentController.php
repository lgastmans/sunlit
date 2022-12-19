<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use NumberFormatter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleOrder;
use App\Models\SaleOrderPayment;
use \App\Http\Requests\StoreSaleOrderPaymentRequest;

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
        if ($request->has('draw'))
            $draw = $request->get('draw');

        $start = 0;
        if ($request->has('start'))
            $start = $request->get("start");

        $length = 10;
        if ($request->has('length')) {
            $length = $request->get("length");
        }

        $order_column = 'paid_at';
        $order_dir = 'ASC';
        $order_arr = array();
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
        $totalRecordswithFilter = SaleOrderPayment::
            where('sale_order_id', '=', $filter_sale_order_id)->count();
        

        // Fetch records
        if ($length < 0)
            $payments = SaleOrderPayment::select('sale_order_payments.*')
                ->where('sale_order_id', '=', $filter_sale_order_id)
                ->orderBy($order_column, $order_dir)
                ->get();
        else
            $payments = SaleOrderPayment::select('sale_order_payments.*')
                ->where('sale_order_id', '=', $filter_sale_order_id)
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();

        $arr = array();

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        foreach($payments as $record)
        {

            $dt = Carbon::parse($record->paid_at);
            // return $dt->toFormattedDateString();

            $arr[] = array(
                "id" => $record->id,
                "amount" => $fmt->formatCurrency($record->amount, "INR"),
                "payment_reference" => $record->reference,
                "payment_date" => $dt->toDateString(),
            );
        }

        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $arr,
            'error' => null
        );
                
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSaleOrderPaymentRequest $request)
    {
        $log = array();
        $log_text = '';

        $order = SaleOrder::find($request->get('sale_order_id'));

        if (is_null($request->get('payment_id')))
        {
            $validatedData = $request->validated();
            $payment = SaleOrderPayment::create($validatedData);

            $dt = Carbon::parse($payment->paid_at);

            $log_text = 'Payment '.$payment->reference.' added, dated '.$dt->toDateString();

            activity()
               //->performedOn($payment)
               ->performedOn($order)
               ->withProperties(['reference'=>$payment->reference, 'amount'=>$payment->amount, 'order_number'=>$order->order_number])
               ->log($log_text);
        }
        else {
            $payment = SaleOrderPayment::find($request->get('payment_id'));
            if ($payment)
            {
                $payment->amount = $request->get('amount');
                $payment->reference = $request->get('reference');
                $payment->paid_at = $request->get('paid_at');
                $payment->save();

                $dt = Carbon::parse($payment->paid_at);

                $log_text = 'Payment '.$payment->reference.' edited, dated '.$dt->toDateString();

                activity()
                   //->performedOn($payment)
                   ->performedOn($order)
                   ->withProperties(['reference'=>$payment->reference, 'amount'=>$payment->amount, 'order_number'=>$order->order_number])
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = SaleOrderPayment::find($id);

        $dt = Carbon::parse($payment->paid_at);

        activity()
           ->performedOn($payment)
           ->withProperties(['paid_at'=>$payment->paid_at, 'reference'=>$payment->reference, 'amount'=>$payment->amount])
           ->log('Payment '.$payment->reference.' dated '.$dt->toDateString().' deleted');

        SaleOrderPayment::destroy($id);

        $log['log_date'] = $dt->toDateString();
        $log['log_text'] = 'Payment '.$payment->reference.' dated '.$dt->toDateString().' deleted'.' by <b>'.Auth::user()->name.'</b>';

        // return response()->json($validatedData);
        return response()->json($log);
        
    }
}
