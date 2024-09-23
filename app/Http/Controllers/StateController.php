<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Requests\StoreStateRequest;
use App\Models\Product;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $user = Auth::user();
        // if ($user->can('list states'))
        return view('states.index');

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

        $order_column = 'name';
        $order_dir = 'ASC';
        $order_arr = [];
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];
            $order_column = $column_arr[$column_index]['data'];
            // if ($column_index==3)
            //     $order_column = "states.name";

            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = State::count();
        $totalRecordswithFilter = State::where('name', 'like', '%'.$search.'%')
            ->count();

        // Fetch records
        if ($length < 0) {
            $states = State::where('states.name', 'like', '%'.$search.'%')
                //->select('states.id', 'states.name', 'states.code', 'freight_zones.name AS freight_zone')
                ->select('states.*', 'freight_zones.name AS freight_zone')
                ->leftJoin('freight_zones', 'freight_zones.id', '=', 'states.freight_zone_id')
                ->orderBy($order_column, $order_dir)
                ->get();
        } else {
            $states = State::where('states.name', 'like', '%'.$search.'%')
                ->select('states.*', 'freight_zones.name AS freight_zone')
                ->leftJoin('freight_zones', 'freight_zones.id', '=', 'states.freight_zone_id')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();
        }

        $arr = [];

        foreach ($states as $record) {

            $arr[] = [
                'id' => $record->id,
                'name' => $record->name,
                'code' => $record->code,
                'abbr' => $record->abbreviation,
                'freight_zone' => $record->freight_zone,

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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStateRequest $request)
    {
        //
        $validatedData = $request->validated();
        $state = State::create($validatedData);
        if ($state) {
            return redirect(route('states'))->with('success', trans('app.record_added', ['field' => 'state']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'state']));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
        $state = State::find($id);
        if ($state) {
            return view('states.show', ['state' => $state]);
        }

        return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'state']));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id): View
    {
        //
        $state = State::find($id);
        if ($state) {
            return view('states.form', ['state' => $state]);
        }

        return view('states.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreStateRequest $request, int $id)
    {
        //
        $validatedData = $request->validated();
        $state = State::whereId($id)->update($validatedData);
        if ($state) {
            return redirect(route('states'))->with('success', trans('app.record_edited', ['field' => 'state']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'state']));

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
        if ($user->can('delete states')) {
            /*
                check if tax present in products
            */
            $count = Product::where('tax_id', $id)->count();

            if ($count > 0) {
                return redirect(route('taxes'))->with('error', trans('error.tax_has_product'));
            }

            State::destroy($id);

            return redirect(route('states'))->with('success', trans('app.record_deleted', ['field' => 'tax']));
        }

        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Display a listing of the resource for select2
     *
     * @return json
     */
    public function getListForSelect2(Request $request): json
    {
        $query = State::query();
        if ($request->has('q')) {
            $query->where('name', 'like', $request->get('q').'%');
        }
        $states = $query->select('id', 'name as text')->get();

        return ['results' => $states];
    }
}
