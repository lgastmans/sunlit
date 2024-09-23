<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Http\Requests\StoreWarehouseRequest;
use App\Models\InventoryMovement;
use App\Models\PurchaseOrder;
use App\Models\SaleOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list warehouses')) {
            return view('warehouses.index');
        }

        return abort(403, trans('error.unauthorized'));

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

        $order_column = 'name';
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
        $totalRecords = Warehouse::count();
        $totalRecordswithFilter = Warehouse::where('contact_person', 'like', '%'.$search.'%')
            ->join('states', 'states.id', '=', 'state_id')
            ->orWhere('warehouses.name', 'like', '%'.$search.'%')
            ->orWhere('city', 'like', '%'.$search.'%')
            ->orWhere('states.name', 'like', '%'.$search.'%')
            ->count();

        // Fetch records
        if ($length < 0) {
            $warehouses = Warehouse::where('contact_person', 'like', '%'.$search.'%')
                ->join('states', 'states.id', '=', 'state_id')
                ->orWhere('warehouses.name', 'like', '%'.$search.'%')
                ->orWhere('city', 'like', '%'.$search.'%')
                ->orWhere('states.name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->select('warehouses.*')
                ->get();
        } else {
            $warehouses = Warehouse::where('contact_person', 'like', '%'.$search.'%')
                ->join('states', 'states.id', '=', 'state_id')
                ->orWhere('warehouses.name', 'like', '%'.$search.'%')
                ->orWhere('city', 'like', '%'.$search.'%')
                ->orWhere('states.name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->select('warehouses.*')
                ->get();
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordswithFilter,
            'data' => $warehouses,
            'error' => null,
        ];

        echo json_encode($response);

        exit;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $warehouse = new Warehouse;

        return view('warehouses.form', ['warehouse' => $warehouse]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreWarehouseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWarehouseRequest $request)
    {
        $validatedData = $request->validated();
        $warehouse = Warehouse::create($validatedData);
        if ($warehouse) {
            return redirect(route('warehouses'))->with('success', trans('app.record_added', ['field' => 'warehouse']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'warehouse']));
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
        if ($user->can('view warehouses')) {
            $warehouse = Warehouse::find($id);
            $entry_filter = InventoryMovement::getMovementFilterList();
            $purchase_order_status = PurchaseOrder::getStatusList();
            $sale_order_status = SaleOrder::getStatusList();
            if ($warehouse) {
                return view('warehouses.show',
                    ['warehouse' => $warehouse,
                        'entry_filter' => $entry_filter,
                        'purchase_order_status' => $purchase_order_status,
                        'sale_order_status' => $sale_order_status,
                    ]);
            }

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'warehouse']));
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
        $warehouse = Warehouse::with('state')->find($id);
        if ($warehouse) {
            return view('warehouses.form', ['warehouse' => $warehouse]);
        }

        return view('warehouses.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreWarehouseRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreWarehouseRequest $request, int $id)
    {
        $validatedData = $request->validated();
        $warehouse = Warehouse::whereId($id)->update($validatedData);
        if ($warehouse) {
            return redirect(route('warehouses'))->with('success', trans('app.record_edited', ['field' => 'warehouse']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'warehouse']));
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
        if ($user->can('delete warehouses')) {
            /*
                check if warehouse present in purchase orders
            */
            $count = PurchaseOrder::where('warehouse_id', $id)->count();

            if ($count > 0) {
                return redirect(route('warehouses'))->with('error', trans('error.warehouse_has_purchase_order'));
            }

            Warehouse::destroy($id);

            return redirect(route('warehouses'))->with('success', trans('app.record_deleted', ['field' => 'warehouse']));
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
        $query = Warehouse::query();
        if ($request->has('q')) {
            $query->where('name', 'like', $request->get('q').'%');
        }
        $warehouse = $query->get(['id', 'name as text']);

        return ['results' => $warehouse];
    }
}
