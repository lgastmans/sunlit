<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Requests\StoreTaxRequest;
use App\Models\Product;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list taxes')) {
            return view('taxes.index');
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
            if ($column_index == 1) {
                $order_column = 'taxes.amount';
            } else {
                $order_column = $column_arr[$column_index]['data'];
            }

            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Tax::count();
        $totalRecordswithFilter = Tax::where('name', 'like', '%'.$search.'%')
            ->orWhere('amount', 'like', '%'.$search.'%')
            ->count();

        // Fetch records
        if ($length < 0) {
            $taxes = Tax::select('id', 'name', 'amount')
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('amount', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        } else {
            $taxes = Tax::select('id', 'name', 'amount')
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('amount', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordswithFilter,
            'data' => $taxes,
            'error' => null,
        ];

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tax = new Tax;

        return view('taxes.form', ['tax' => $tax]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaxRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData = Arr::except($validatedData, ['display_amount']);
        $tax = Tax::create($validatedData);
        if ($tax) {
            return redirect(route('taxes'))->with('success', trans('app.record_added', ['field' => 'tax']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'tax']));
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
     */
    public function edit(int $id): View
    {
        $tax = Tax::find($id);
        if ($tax) {
            return view('taxes.form', ['tax' => $tax]);
        }

        return view('taxes.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTaxRequest $request, int $id)
    {
        $validatedData = $request->validated();
        $validatedData = Arr::except($validatedData, ['display_amount']);
        $tax = Tax::whereId($id)->update($validatedData);
        if ($tax) {
            return redirect(route('taxes'))->with('success', trans('app.record_edited', ['field' => 'tax']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'tax']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        if ($user->can('delete taxes')) {
            /*
                check if tax present in products
            */
            $count = Product::where('tax_id', $id)->count();

            if ($count > 0) {
                return redirect(route('taxes'))->with('error', trans('error.tax_has_product'));
            }

            Tax::destroy($id);

            return redirect(route('taxes'))->with('success', trans('app.record_deleted', ['field' => 'tax']));
        }

        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Display a listing of the resource for select2
     */
    public function getListForSelect2(Request $request): json
    {
        $query = Tax::query();
        if ($request->has('q')) {
            $query->where('name', 'like', $request->get('q').'%');
        }
        $taxes = $query->select('id', 'name as text')->get();

        return ['results' => $taxes];
    }
}
