<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use \App\Http\Requests\StoreSupplierRequest;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list suppliers'))
            return view('suppliers.index');
    
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
            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Supplier::get()->count();
        $totalRecordswithFilter = Supplier::where('contact_person', 'like', '%'.$search.'%')
            ->orWhere('company', 'like', '%'.$search.'%')
            ->orWhere('address', 'like', '%'.$search.'%')
            ->get()
            ->count();
        

        // Fetch records
        if ($length < 0)
            $suppliers = Supplier::where('contact_person', 'like', '%'.$search.'%')
                ->orWhere('company', 'like', '%'.$search.'%')
                ->orWhere('address', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        else
            $suppliers = Supplier::where('contact_person', 'like', '%'.$search.'%')
                ->orWhere('company', 'like', '%'.$search.'%')
                ->orWhere('address', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();


        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $suppliers,
            'error' => null
        );
        echo json_encode($response);

        exit;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supplier = new Supplier();
        return view('suppliers.form', ['supplier' => $supplier]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreSupplierRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSupplierRequest $request)
    {
        $validatedData = $request->validated();
        $supplier = Supplier::create($validatedData);
        if ($supplier){
            return redirect(route('suppliers'))->with('success', trans('app.record_added', ['field' => 'supplier']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'supplier']));
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
        if ($user->can('view suppliers')){
            $supplier = Supplier::with('products','products.inventory')->find($id);
            if ($supplier){
                return view('suppliers.show', ['supplier' => $supplier]);
            }
            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'supplier']));
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
        $supplier = Supplier::with('state')->find($id);
        if ($supplier){
            return view('suppliers.form', ['supplier' => $supplier]);
        }
        return view('suppliers.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreSupplierRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSupplierRequest $request, $id)
    {
        $validatedData = $request->validated();
        $supplier = Supplier::whereId($id)->update($validatedData);
        if ($supplier){
            return redirect(route('suppliers'))->with('success', trans('app.record_edited', ['field' => 'supplier']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'supplier']));
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
        
        if ($user->can('delete suppliers'))
        {
            /*
                check if supplier present in purchase orders
            */
            $po_count = PurchaseOrder::where('supplier_id', $id)->get()->count();

            if ($po_count > 0)
            {
                return redirect(route('suppliers'))->with('error', trans('error.supplier_has_purchase_order'));
            }


            /*
                related products are deleted through the Eloquent Model Event
                (see the Supplier model)
            */
            Supplier::destroy($id);
            return redirect(route('suppliers'))->with('success', trans('app.record_deleted', ['field' => 'supplier']));

        }

        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Display a listing of the resource for select2
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function getListForSelect2(Request $request)
    {
        $query = Supplier::query();
        if ($request->has('q')){
            $query->where('company', 'like', $request->get('q').'%');
        }
        $suppliers = $query->select('id', 'company as text')->get();
        return ['results' => $suppliers];
    }  
    
    
    public function stats($id)
    {
        $monthly_data = DB::table('purchase_orders')
                    ->select(
                                DB::raw("year(ordered_at) as ordered_year"), 
                                DB::raw("month(ordered_at) as ordered_month"), 
                                DB::raw("sum(amount_usd) as amount_usd"), 
                                DB::raw("sum(amount_inr) as amount_inr"), 
                                DB::raw("count(id) as number_orders"))
                    ->where('supplier_id', '=', $id)
                    ->where('status', '>=', PurchaseOrder::ORDERED)
                    ->groupBy(DB::raw("year(ordered_at)"), DB::raw("month(ordered_at)"))
                    ->get();

        $total_orders = 0;
        $total_amount['inr'] = 0;
        
        $graph_data['orders'] = [];
        $graph_data['amount_inr'] = [];
        for($i = 0; $i<12; $i++){
            $graph_data['amount_inr'][$i] = 0;
            $graph_data['orders'][$i] = 0;
        }
        foreach($monthly_data as $data){
            $total_orders += $data->number_orders;
            $total_amount['inr'] += $data->amount_inr;
            $graph_data['orders'][$data->ordered_month-1] = $data->number_orders;
            $graph_data['amount_inr'][$data->ordered_month-1] =$data->amount_inr;
        }
        return response()->json(['success'=>'true','code'=>200, 'message'=> 'OK', 'data' => ['total_orders' => $total_orders, 'total_amount' => $total_amount, 'graph_data' => $graph_data]]); 
    }
}
