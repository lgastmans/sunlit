<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use NumberFormatter;
use App\Models\Dealer;
use App\Models\SaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Http\Requests\StoreDealerRequest;



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
        if ($user->can('list dealers'))
            return view('dealers.index');
    
        return abort(403, trans('error.unauthorized'));

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

        $order_column = 'company';
        $order_dir = 'ASC';
        $order_arr = array();
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            $order_column = $column_arr[$column_index]['data'];
            if ($column_index==3)
                $order_column = "states.name";
            
            
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
        if ($length < 0)
            $dealers = Dealer::select('dealers.id AS dealer_id', 'company', 'contact_person', 'city', 'states.name as state_name', 'email', 'phone')
                ->where('contact_person', 'like', '%'.$search.'%')
                ->join('states', 'states.id', '=', 'dealers.state_id')
                ->orWhere('company', 'like', '%'.$search.'%')
                ->orWhere('city', 'like', '%'.$search.'%')
                ->orWhere('states.name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        else
            $dealers = Dealer::select('dealers.id AS dealer_id', 'company', 'contact_person', 'city', 'states.name as state_name', 'email', 'phone')
                ->where('contact_person', 'like', '%'.$search.'%')
                ->join('states', 'states.id', '=', 'state_id')
                ->orWhere('company', 'like', '%'.$search.'%')
                ->orWhere('city', 'like', '%'.$search.'%')
                ->orWhere('states.name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();

        $arr = array();

        foreach($dealers as $record)
        {

            $arr[] = array(
                "id" => $record->dealer_id,
                "company" => $record->company,
                "contact_person" => $record->contact_person,
                "city" => $record->city,
                "state_name" => $record->state_name,
                "email" => $record->email,
                "phone" => $record->phone
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


    public function getListForLedger(Request $request)
    {
        $vdata = array();

        $ret = array(
                "data"=>array(),
                "footer"=>array("debit_total"=>0, "credit_total"=>0),
                "customer"=>array("company"=>'',"address"=>'')
            );

        $dealer_id = 0;
        if ($request->has('dealer_id')) {
            $dealer_id = $request->get("dealer_id");
        }

        /**
         * get the dealer's invoices
        */
        $query = SaleOrder::query();
        $query->select('sale_orders.id, sale_orders.dispatched_at', 'sale_orders.order_number_slug', 'sale_orders.amount', 'sale_orders.transport_charges');
        $query->where('sale_orders.status', '>=', '4');
        $query->where('sale_orders.dealer_id', '=', $dealer_id);
        $query->orderBy('sale_orders.dispatched_at','ASC');
        $query->orderBy('sale_orders.order_number_slug','ASC');        
        $rows = $query->get();

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, ''); 

        $arr = array();
        $footer = array('label'=>'Totals', 'total_quantity'=>0, 'total_taxable_value'=>0, 'total_tax_amount'=>0, 'total_amount'=>0);

        foreach($rows as $obj)
        {
            $row = array();

            $total = $obj->amount + $obj->transport_charges;

            $row["vch_date"]    = date('d-m-Y', strtotime($obj->dispatched_at));
            $row["particulars"] = $obj->order_number_slug;
            $row["vch_type"]    = "Sales";
            $row["vch_no"]      = $obj->order_number_slug;
            $row["debit"]       = $fmt->formatCurrency($total, "INR");
            $row["credit"]      = 0;
            
            $vdata[] = $row;
            
            /**
             * get the payments against the invoice
             */
            $sale_order = SaleOrder::findById($obj->id);
            
            $query = SaleOrderPayment::query();
            $query->select('');
            $query->join('sale_orders', 'sale_orders.id', '=', 'sale_order_id');


        }

//print_r($vdata);die();

        /**
         * sort the data by date
         */
        usort($vdata, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d-m-Y', $a['vch_date']);
            $dateB = DateTime::createFromFormat('d-m-Y', $b['vch_date']);
            return $dateA >= $dateB;
        });        

        // usort($vdata, function ($a, $b) {
        //     $dateA = Carbon::createFromFormat('d-m-Y', $a['vch_date']);
        //     $dateB = Carbon::createFromFormat('d-m-Y', $b['vch_date']);
        //     return $dateA >= $dateB;
        // });

        $response = array(
            //"draw" => $draw,
            //"recordsTotal" => $totalRecords,
            //"recordsFiltered" => $totalRecordswithFilter,
            "data" => array("data"=>$vdata, "footer"=>$footer), //$arr,

            "error" => null
        );
        return response()->json($response);
    }


    public function ledger(Request $request)
    {
        return view('dealers.ledger');
    }


    public function ledgerSummary(Request $request)
    {
        return "ledger summary";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dealer = new Dealer();
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
        if ($dealer){
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
    public function show($id)
    {
        $user = Auth::user();
        if ($user->can('view dealers')){
            $dealer = Dealer::with('state')->find($id);
            if ($dealer)
                return view('dealers.show', ['dealer' => $dealer]);

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
    public function edit($id)
    {
        $dealer = Dealer::with('state')->find($id);
        if ($dealer){
            return view('dealers.form', ['dealer' => $dealer]);
        }
        return view('dealers.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreDealerRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreDealerRequest $request, $id)
    {
        $validatedData = $request->validated();
        $dealer = Dealer::whereId($id)->update($validatedData);
        if ($dealer){
            return redirect(route('dealers'))->with('success', trans('app.record_edited', ['field' => 'dealers']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'dealers']));
    }


        /**
     * Display a listing of the resource for select2
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function getListForSelect2(Request $request)
    {
        $query = Dealer::query();
        if ($request->has('q')){
            $query->where('company', 'like', $request->get('q').'%');
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
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->can('delete dealers')){
            Dealer::destroy($id);
            return redirect(route('dealers'))->with('success', trans('app.record_deleted', ['field' => 'dealers']));
        }
        return abort(403, trans('error.unauthorized'));
    }
}
