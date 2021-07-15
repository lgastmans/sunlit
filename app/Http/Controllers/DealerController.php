<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Http\Requests\StoreSupplierRequest;



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
            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Dealer::get()->count();
        $totalRecordswithFilter = Dealer::where('contact_person', 'like', '%'.$search.'%')
            ->orWhere('company', 'like', '%'.$search.'%')
            ->orWhere('address', 'like', '%'.$search.'%')
            ->get()
            ->count();
        

        // Fetch records
        if ($length < 0)
            $dealers = Dealer::where('contact_person', 'like', '%'.$search.'%')
                ->orWhere('company', 'like', '%'.$search.'%')
                ->orWhere('address', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        else
            $dealers = Dealer::where('contact_person', 'like', '%'.$search.'%')
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
            "data" => $dealers,
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
        $dealer = new Dealer();
        return view('dealers.form', ['dealer' => $dealer]);
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
        $dealer = Dealer::with('state')->find($id);
        if ($dealer){
            return view('dealers.form', ['dealer' => $dealer]);
        }
        return view('dealers.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreSupplierRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSupplierRequest $request, $id)
    {
        $validatedData = $request->validated();
        $dealer = Dealer::whereId($id)->update($validatedData);
        if ($dealer){
            return redirect(route('dealers'))->with('success', trans('app.record_edited', ['field' => 'dealers']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'dealers']));
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
