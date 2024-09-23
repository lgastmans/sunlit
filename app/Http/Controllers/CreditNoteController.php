<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCreditNoteRequest;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Inventory;
use App\Models\SaleOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;
use Spatie\Activitylog\Models\Activity;

class CreditNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = CreditNote::getStatusList();

        return view('credit_notes.index', ['status' => $status]);
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

        $order_column = 'credit_note_number';
        $order_dir = 'ASC';
        $order_arr = [];
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];

            switch ($column_index) {
                case 1:
                    $order_column = 'credit_notes.warehouse_id';
                    break;
                case 2:
                    $order_column = 'dealers.company';
                    break;
                case 3:
                    $order_column = 'states.name';
                    break;
                case 9:
                    $order_column = 'users.name';
                    break;
                default:
                    $order_column = $column_arr[$column_index]['data'];

                    $order_dir = $order_arr[0]['dir'];
            }
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = CreditNote::count();

        $query = CreditNote::query();
        $query->join('dealers', 'dealers.id', '=', 'dealer_id');
        $query->join('warehouses', 'warehouses.id', '=', 'warehouse_id');
        $query->join('users', 'users.id', '=', 'user_id');
        $query->join('states', 'states.id', '=', 'dealers.state_id');

        if ($request->has('filter_warehouse_id')) {
            $query->where('credit_notes.warehouse_id', '=', $request->filter_warehouse_id);
        }

        if (! empty($column_arr[0]['search']['value'])) {
            $query->where('credit_notes.credit_note_number', 'like', '%'.$column_arr[0]['search']['value'].'%');
        }
        if (! empty($column_arr[1]['search']['value'])) {
            $query->where('warehouses.name', 'like', '%'.$column_arr[1]['search']['value'].'%');
        }
        if (! empty($column_arr[2]['search']['value'])) {
            $query->where('dealers.company', 'like', '%'.$column_arr[2]['search']['value'].'%');
        }
        if (! empty($column_arr[3]['search']['value'])) {
            $query->where('states.name', 'like', '%'.$column_arr[3]['search']['value'].'%');
        }
        if (! empty($column_arr[6]['search']['value'])) {
            $query->where('credit_notes.amount', 'like', $column_arr[6]['search']['value'].'%');
        }
        if (! empty($column_arr[7]['search']['value']) && $column_arr[7]['search']['value'] != 'all') {
            $query->where('credit_notes.status', 'like', $column_arr[7]['search']['value']);
        }
        if (! empty($column_arr[8]['search']['value'])) {
            $query->where('users.name', 'like', $column_arr[8]['search']['value'].'%');
        }

        if ($request->has('search')) {
            $search = $request->get('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('credit_notes.credit_note_number', 'like', '%'.$search.'%')
                    ->orWhere('credit_notes.amount', 'like', $search.'%')
                    ->orWhere('dealers.company', 'like', '%'.$search.'%')
                    ->orWhere('users.name', 'like', '%'.$search.'%')
                    ->orWhere('states.name', 'like', '%'.$search.'%');
            });
        }

        if ($request->has('filter_column')) {
            $filter_column = $request->get('filter_column');
            $filter_from = $request->get('filter_from');
            $filter_to = $request->get('filter_to');

            if ((! is_null($filter_from)) && (! is_null($filter_to))) {
                $filter_from = Carbon::createFromFormat('Y-m-d', $filter_from)->toDateString();
                $filter_to = Carbon::createFromFormat('Y-m-d', $filter_to)->toDateString();

                if ($filter_column == 'confirmed') {
                    $query->whereBetween('credit_notes.confirmed_at', [$filter_from, $filter_to]);
                } else {
                    $query->whereBetween('credit_notes.created_at', [$filter_from, $filter_to]);
                }
            }
        }

        $totalRecordswithFilter = $query->count();

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $query->orderBy($order_column, $order_dir);
        //$sql = $query->toSql();dd($sql);
        $orders = $query->get(['credit_notes.*', 'warehouses.name', 'dealers.company', 'users.name']);

        $arr = [];
        foreach ($orders as $order) {
            $total_amount = '';
            if (isset($order->amount)) {
                $curOrder = CreditNote::find($order->id);
                $curOrder->calculateTotals();
                $total_amount = $curOrder->total;
            }

            $arr[] = [
                'id' => $order->id,
                'credit_note_number' => $order->credit_note_number,
                'credit_note_number_slug' => $order->credit_note_number_slug,
                'warehouse' => $order->warehouse->name,
                'dealer' => (isset($order->dealer) ? $order->dealer->company : ''),
                'state' => (isset($order->dealer->state) ? $order->dealer->state->name : ''),
                'created_at' => $order->display_created_at,
                'confirmed_at' => $order->display_confirmed_at,
                'amount' => $total_amount,
                'status' => $order->display_status,
                'user' => $order->user->display_name,
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
        // $user = Auth::user();
        // if ($user->can('edit sale orders')){

        $credit_note = new CreditNote;

        $credit_note_number_count = \Setting::get('credit_note.credit_note_number') + 1;
        $credit_note_number = \Setting::get('credit_note.prefix').$credit_note_number_count.\Setting::get('credit_note.suffix');

        return view('credit_notes.form', ['credit_note' => $credit_note, 'credit_note_number' => $credit_note_number, 'credit_note_number_count' => $credit_note_number_count, 'invoice_date' => date('Y-m-d')]);
        // }
        // return abort(403, trans('error.unauthorized'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCreditNoteRequest $request)
    {
        $validatedData = $request->validated();

        $credit_note = CreditNote::create($validatedData);

        if ($credit_note) {

            /**
             * copy the products of the invoice if it is against an invoice
             */
            if ($credit_note->is_against_invoice == 1) {
                $invoice = SaleOrder::where('order_number', '=', $credit_note->invoice_number)->first();
                if ($invoice) {
                    foreach ($invoice->items as $item) {
                        $credit_note_item = new CreditNoteItem;
                        $credit_note_item->credit_note_id = $credit_note->id;
                        $credit_note_item->product_id = $item->product_id;
                        $credit_note_item->quantity = $item->quantity_ordered;
                        $credit_note_item->price = $item->selling_price;
                        $credit_note_item->tax = $item->tax;
                        $credit_note_item->save();
                    }
                }
            }

            $credit_note->remarks = \Setting::get('credit_note.remarks');
            $credit_note->update();

            $cn_number_count = \Setting::get('credit_note.credit_note_number') + 1;
            \Setting::set('credit_note.credit_note_number', $cn_number_count);
            \Setting::save();

            activity()
                ->performedOn($credit_note)
                ->withProperties(['credit_note_number' => $credit_note->credit_note_number, 'status' => $credit_note->status])
                ->log('Created Credit Note');

            return redirect(route('credit-notes.show', $credit_note->credit_note_number_slug));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'credit note']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CreditNote  $creditNote
     * @return \Illuminate\Http\Response
     */
    public function show($credit_note_number_slug)
    {
        //$user = Auth::user();
        //if ($user->can('view sale orders')){
        $quote = CreditNote::where('credit_note_number_slug', '=', $credit_note_number_slug)->first();
        $quote->calculateTotals();

        $activities = Activity::where('subject_id', $quote->id)
            ->where('subject_type', 'App\Models\CreditNote')
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($quote) {
            return view('credit_notes.show', ['order' => $quote, 'activities' => $activities, 'grand_total' => $quote->total_unfmt]);
        }

        return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'credit note']));
        //}
        //return abort(403, trans('error.unauthorized'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(CreditNote $creditNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCreditNoteRequest  $request
     * @param  \App\Models\CreditNote  $creditNote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (($request->get('field') == 'amount') || ($request->get('field') == 'quantity')) {
            $order = CreditNote::find($id);
            $items = CreditNoteItem::where('credit_note_id', '=', $id)->get();

            /*
                set amount to calculated field total_unfmt
            */
            $order->calculateTotals();
            $order->amount = $order->total_unfmt;

            $order->update();

            return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'field' => $request->get('field'), 'total_cost' => $order->total_unfmt]);
        }

        if ($request->get('field') == 'remarks') {
            $order = CreditNote::find($id);
            $order->remarks = $request->get('value');
            $order->update();
        }
    }

    /**
     * Update the confirmed_at and status
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirmed(Request $request, $id)
    {
        $validated = $request->validate([
            'confirmed_at' => 'required|date',
        ]);
        $order = CreditNote::find($id);

        $order->confirmed_at = $request->get('confirmed_at');
        $order->status = CreditNote::CONFIRMED;

        activity()
            ->performedOn($order)
            ->withProperties(['credit_note_number' => $order->credit_note_number, 'status' => $order->status])
            ->log('Status updated to <b>Confirmed</b>');

        $order->update();

        $inventory = new Inventory;
        $inventory->updateStock($order);

        return redirect(route('credit-notes.show', $order->credit_note_number_slug))->with('success', 'credit note confirmed');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CreditNote  $creditNote
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $order = CreditNote::find($id);

        activity()
            ->performedOn($order)
            ->withProperties(['credit_note_number' => $order->credit_note_number, 'status' => $order->status])
            ->log('Credit Note deleted');

        $order->items()->delete();
        $order->delete();

        if ($request->ajax()) {
            return response()->json(['deleted successfully '.$order->credit_note_number_slug]);
        } else {
            return redirect(route('credit-notes'))->with('success', trans('app.record_deleted', ['field' => 'Credit Note']));
        }
    }

    public function proforma($credit_note_number_slug)
    {
        $settings = \Setting::all();

        $order = CreditNote::where('credit_note_number_slug', '=', $credit_note_number_slug)->first();
        $order->calculateTotals();

        return view('credit_notes.view_proforma', ['order' => $order, 'settings' => $settings, 'not_pdf' => true]);
    }

    public function exportProformaToPdf($credit_note_number_slug)
    {
        $settings = \Setting::all();

        $order = CreditNote::where('credit_note_number_slug', '=', $credit_note_number_slug)->first();
        $order->calculateTotals();
        view()->share('order', $order);
        view()->share('settings', $settings);
        $pdf = PDF::loadView('credit_notes.proforma', ['order' => $order]);

        // download PDF file with download method
        return $pdf->download($credit_note_number_slug.' - '.$order->dealer->company.'.pdf');
    }
}
