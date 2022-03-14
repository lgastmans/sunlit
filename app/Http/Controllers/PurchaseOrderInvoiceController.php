<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderInvoice;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrderInvoiceItem;
use \App\Http\Requests\StorePurchaseOrderInvoiceRequest;



class PurchaseOrderInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list purchase orders')){
            $status = PurchaseOrderInvoice::getStatusList();
            return view('purchase_order_invoices.index', ['status' => $status]);

        }
    
        return abort(403, trans('error.unauthorized'));
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

        $order_column = 'invoice_number';
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

        $totalRecords = PurchaseOrderInvoice::count();
        

        $query = PurchaseOrderInvoice::query();
        $query->join('users', 'users.id', '=', 'user_id');
        $query->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_id');

        if (!empty($column_arr[0]['search']['value'])){
            $query->where('purchase_order_invoices.invoice_number', 'like', '%'.$column_arr[0]['search']['value'].'%');
        }
        if (!empty($column_arr[1]['search']['value'])){
            $query->where('purchase_orders.order_number', 'like', '%'.$column_arr[1]['search']['value'].'%');
        }
        if (!empty($column_arr[2]['search']['value'])){
            $query->where('purchase_order_invoices.shipped_at', 'like', convertDateToMysql($column_arr[2]['search']['value']));
        }
        if (!empty($column_arr[3]['search']['value'])){
            $query->where('purchase_order_invoices.amount_inr', 'like', $column_arr[3]['search']['value'].'%');
        }
        if (!empty($column_arr[4]['search']['value']) && $column_arr[4]['search']['value'] != "all"){
            $query->where('purchase_order_invoices.status', 'like', $column_arr[4]['search']['value']);
        }
        if (!empty($column_arr[5]['search']['value'])){
            $query->where('users.name', 'like', $column_arr[5]['search']['value'].'%');
        }
        
        if ($request->has('search')){
            $search = $request->get('search')['value'];
            $query->where( function ($q) use ($search){
                $q->where('purchase_orders.order_number', 'like', '%'.$search.'%')
                    ->orWhere('purchase_order_invoices.amount_inr', 'like', $search.'%')
                    ->orWhere('purchase_order_invoices.invoice_number', 'like', '%'.$search.'%');
            });    
        }

        $totalRecordswithFilter = $query->count();


        if ($length > 0)
            $query->skip($start)->take($length);

        $query->orderBy($order_column, $order_dir);
        $invoices = $query->select('purchase_order_invoices.*', 'purchase_orders.order_number')->get();

        $arr = array();
        foreach($invoices as $invoice)
        {           
            $arr[] = array(
                "id" => $invoice->id,
                "invoice_number" => $invoice->invoice_number,
                "invoice_number_slug" => $invoice->invoice_number_slug,
                "order_number" => $invoice->purchase_order->order_number,
                "shipped_at" => $invoice->display_shipped_at,
                "amount" => (isset($invoice->amount_inr)) ? trans('app.currency_symbol_inr')." ".$invoice->amount_inr : "",
                "status" => $invoice->display_status,
                "user" => $invoice->user->display_name
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseOrderInvoiceRequest $request)
    {
        $validatedData = $request->validated();
        $purchase_order = PurchaseOrder::find($request->purchase_order_id);
        /*
        $purchase_order->status = PurchaseOrder::SHIPPED;
        $purchase_order->save();
        */

        $invoice = new PurchaseOrderInvoice;
        $invoice->purchase_order_id = $purchase_order->id;
        $invoice->order_exchange_rate = $purchase_order->order_exchange_rate;
        $invoice->status = PurchaseOrderInvoice::SHIPPED;
        $invoice->invoice_number = $validatedData['invoice_number'];
        $invoice->shipped_at = $validatedData['shipped_at'];
        $invoice->user_id = $validatedData['user_id'];
        $invoice->invoice_number_slug =  $validatedData['invoice_number_slug'];
        $invoice->save();

        $invoice_id = $invoice->id;
        $invoice_number = $invoice->invoice_number;
        $invoice_number_slug = $invoice->invoice_number_slug;

        $amount_usd = 0;
        foreach($request->products as $product_id => $quantity_shipped){
            $product = Product::find($product_id);
            $invoice_item = new PurchaseOrderInvoiceItem;
            $invoice_item->purchase_order_invoice_id = $invoice_id;
            $invoice_item->product_id = $product_id;
            $invoice_item->quantity_shipped = $quantity_shipped;
            $purchase_order_item = PurchaseOrderItem::where('purchase_order_id', $purchase_order->id)->where('product_id', $product_id)->first();
            $invoice_item->buying_price = $purchase_order_item->buying_price;

            $invoice_item->customs_duty = $invoice_item->buying_price * $invoice_item->quantity_shipped * ($product->category->customs_duty /100);
            $invoice_item->social_welfare_surcharge = $invoice_item->customs_duty * ($product->category->social_welfare_surcharge /100);
            $invoice_item->igst = ($invoice_item->buying_price * $quantity_shipped + $invoice_item->customs_duty + $invoice_item->social_welfare_surcharge) * ($product->category->igst /100);
         
            $charges = [
                'customs_duty'=> $product->category->customs_duty,
                'social_welfare_surcharge'=> $product->category->social_welfare_surcharge,
                'igst'=> $product->category->igst,
            ];
            $invoice_item->charges = $charges;

            $invoice_item->save();

            $amount_usd += $invoice_item->quantity_shipped * $invoice_item->buying_price;
        }

        $inventory = new Inventory();
        $inventory->updateStock($invoice);        

        $invoice->amount_usd = $amount_usd;
        $invoice->amount_inr = $invoice->amount_usd * $invoice->order_exchange_rate;
        $invoice->update();
        
        return response()->json(['success'=>'true','code'=>200, 'message'=> 'OK', 'redirect' => route('purchase-order-invoices.show', $invoice_number_slug)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_number_slug)
    {
        $user = Auth::user();
        if ($user->can('view purchase orders')){
            $po = PurchaseOrderInvoice::with('purchase_order')->where('invoice_number_slug', '=', $invoice_number_slug)->first();
            $purchase_order = $po->purchase_order;
            $invoice = PurchaseOrderInvoice::with(['items', 'items.product'])->where('invoice_number_slug', '=', $invoice_number_slug)->first();
            if ($invoice)
                return view('purchase_order_invoices.show', ['invoice' => $invoice, 'purchase_order' => $purchase_order ]);

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'purchase order']));
        }
        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseOrderInvoice  $purchaseOrderInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseOrderInvoice $purchaseOrderInvoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseOrderInvoice  $purchaseOrderInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseOrderInvoice $purchaseOrderInvoice)
    {
        //
    }


    /**
     * Update the shipped_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function customs(Request $request, $id)
    {
        $validated = $request->validate([
            'customs_at' => 'required|date',
            'boe_number' => 'required',
        ]);
        $invoice = PurchaseOrderInvoice::find($id);
        $invoice->customs_at = $request->get('customs_at');
        $invoice->boe_number = $request->get('boe_number');
        $invoice->status = PurchaseOrderInvoice::CUSTOMS;
        $invoice->update();
        return redirect(route('purchase-order-invoices.show', $invoice->invoice_number_slug))->with('success', 'order at customs'); 
    }

    /**
     * Update the cleared_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cleared(Request $request, $id)
    {
        $validated = $request->validate([
            'cleared_at' => 'required|date',
            'customs_exchange_rate' => 'required',
        ]);
        $invoice = PurchaseOrderInvoice::find($id);
        $invoice->cleared_at = $request->get('cleared_at');
        $invoice->customs_exchange_rate = $request->get('customs_exchange_rate');

        $invoice->customs_duty = $request->get('total_customs_duty_inr');
        $invoice->social_welfare_surcharge = $request->get('total_social_welfare_surcharge_inr');
        $invoice->igst = $request->get('total_igst_inr');
        $invoice->landed_cost = $request->get('landed_cost_inr');


        $invoice->status = PurchaseOrderInvoice::CLEARED;
        $invoice->update();

        
        $invoice->updateItemsBuyingPrice($id);


        return redirect(route('purchase-order-invoices.show', $invoice->invoice_number))->with('success', 'order cleared'); 
    }

    /**
     * Update the shipped_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function received(Request $request, $id)
    {
        $invoice = PurchaseOrderInvoice::find($id);
        $invoice_number = $invoice->invoice_number;
        $invoice->received_at = $request->get('received_at');
        $invoice->status = PurchaseOrderInvoice::RECEIVED;
        $invoice->update();

        $inventory = new Inventory();
        $inventory->updateStock($invoice);

        return redirect(route('purchase-order-invoices.show', $invoice_number))->with('success', 'order received'); 
    }

    /**
     * Update the paid_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paid(Request $request, $id)
    {
        $validated = $request->validate([
            'paid_at' => 'required|date',
            'paid_exchange_rate' => 'required',
        ]);

        $invoice = PurchaseOrderInvoice::find($id);
        $invoice_number = $invoice->invoice_number;
        $invoice->paid_at = $request->get('paid_at');
        $invoice->paid_exchange_rate = $request->get('paid_exchange_rate');
        $invoice->paid_cost = $invoice->amount_usd * $request->get('paid_exchange_rate');
        $invoice->payment_reference = $request->get('payment_reference');
        $invoice->status = PurchaseOrderInvoice::PAID;
        $invoice->update();

        $invoice->updateItemsPaidPrice($id);

        return redirect(route('purchase-order-invoices.show', $invoice_number))->with('success', 'order received'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseOrderInvoice  $purchaseOrderInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseOrderInvoice $purchaseOrderInvoice)
    {
        //
    }
}
