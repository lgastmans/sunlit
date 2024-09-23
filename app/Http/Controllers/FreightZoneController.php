<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Requests\StoreFreightZoneRequest;
use App\Models\FreightZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FreightZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Auth::user();
        if ($user->can('list tranport zones')) {
            return view('freight-zones.index');
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
        $totalRecords = FreightZone::count();
        $totalRecordswithFilter = FreightZone::where('name', 'like', '%'.$search.'%')
            ->count();

        // Fetch records
        if ($length < 0) {
            $zones = FreightZone::where('name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        } else {
            $zones = FreightZone::where('name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();
        }

        $arr = [];

        foreach ($zones as $record) {

            $arr[] = [
                'id' => $record->id,
                'name' => $record->name,
                'rate_per_kg' => $record->rate_per_kg,

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
    public function create(): View
    {
        //
        $zone = new FreightZone;

        return view('freight-zones.form', ['zone' => $zone]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFreightZoneRequest $request)
    {
        //
        $validatedData = $request->validated();
        $zone = FreightZone::create($validatedData);
        if ($zone) {
            return redirect(route('freight-zones'))->with('success', trans('app.record_added', ['field' => 'zone']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'zone']));

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
        $user = Auth::user();
        if ($user->can('view tranport zones')) {
            $zone = FreightZone::find($id);
            if ($zone) {
                return view('freight-zones.show', ['zone' => $zone]);
            }

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'zone']));
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
        //
        $zone = FreightZone::find($id);
        if ($zone) {
            return view('freight-zones.form', ['zone' => $zone]);
        }

        return view('freight-zones.index');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreFreightZoneRequest $request, int $id)
    {
        //
        $validatedData = $request->validated();
        $zone = FreightZone::whereId($id)->update($validatedData);
        if ($zone) {
            return redirect(route('freight-zones'))->with('success', trans('app.record_edited', ['field' => 'zone']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'zone']));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
        // $user = Auth::user();
        // if ($user->can('delete zones')){
        FreightZone::destroy($id);

        return redirect(route('freight-zones'))->with('success', trans('app.record_deleted', ['field' => 'zone']));

        // }
        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Display a listing of the resource for select2
     *
     * @return json
     */
    public function getListForSelect2(Request $request): json
    {
        $query = FreightZone::query();
        if ($request->has('q')) {
            $query->where('name', 'like', $request->get('q').'%');
        }
        $rows = $query->select('id', 'name as text')->get();

        return ['results' => $rows];
    }
}
