<?php

namespace App\Http\Controllers;

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
        $purchase_order->status = PurchaseOrder::SHIPPED;

        $invoice = new PurchaseOrderInvoice;
        $invoice->purchase_order_id = $purchase_order->id;
        $invoice->order_exchange_rate = $purchase_order->order_exchange_rate;
        $invoice->status = PurchaseOrderInvoice::SHIPPED;
        $invoice->due_at = $validatedData['due_at'];
        $invoice->invoice_number = $validatedData['invoice_number'];
        $invoice->shipped_at = $validatedData['shipped_at'];
        $invoice->tracking_number = $validatedData['tracking_number'];
        $invoice->courier = $validatedData['courier'];
        $invoice->user_id = $validatedData['user_id'];
        $invoice_id = $invoice->save();

        $amount_usd = 0;
        foreach($request->products as $product_id => $quantity_shipped){
            $invoice_item = new PurchaseOrderInvoiceItem;
            $invoice_item->purchase_order_invoice_id = $invoice_id;
            $invoice_item->product_id = $product_id;
            $invoice_item->quantity_shipped = $quantity_shipped;
            $purchase_order_item = PurchaseOrderItem::where('purchase_order_id', $purchase_order->id)->where('product_id', $product_id)->first();
            $invoice_item->selling_price = $purchase_order_item->selling_price;
            $invoice_item->save();

            $amount_usd += $invoice_item->quantity_shipped * $invoice_item->selling_price;
        }

        $invoice->amount_usd = $amount_usd;
        $invoice->amount_inr = $invoice->amount_usd * $invoice->order_exchange_rate;
        $invoice->update();
        

        return redirect(route('purchase-order-invoices.show', $invoice->invoice_number)); 
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_number)
    {
        $user = Auth::user();
        if ($user->can('view purchase orders')){
            $po = PurchaseOrderInvoice::with('purchase_order')->where('invoice_number', '=', $invoice_number)->first();
            $purchase_order = $po->purchase_order;
            $invoice = PurchaseOrderInvoice::with(['items', 'items.product'])->where('invoice_number', '=', $invoice_number)->first();
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
        return redirect(route('purchase-order-invoices.show', $invoice->invoice_number))->with('success', 'order at customs'); 
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
        $invoice->customs_duty = $invoice->amount_inr_customs * \Setting::get('purchase_order.customs_duty') / 100;
        $invoice->social_welfare_surcharge = $invoice->customs_duty * \Setting::get('purchase_order.social_welfare_surcharge') / 100;
        $invoice->igst = ($invoice->amount_inr + $invoice->customs_duty + $invoice->social_welfare_surcharge )* \Setting::get('purchase_order.igst') / 100;
        $invoice->bank_and_transport_charges = $invoice->amount_inr * \Setting::get('purchase_order.transport') / 100;
        $charges = [
            'customs_duty'=> \Setting::get('purchase_order.customs_duty'),
            'social_welfare_surcharge'=> \Setting::get('purchase_order.social_welfare_surcharge'),
            'igst'=> \Setting::get('purchase_order.igst'),
            'transport'=> \Setting::get('purchase_order.transport'),
        ];
        $invoice->charges = json_encode($charges);
        $invoice->status = PurchaseOrderInvoice::CLEARED;
        $invoice->update();
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
        $invoice->received_at = $request->get('received_at');
        $invoice->status = PurchaseOrderInvoice::RECEIVED;
        $invoice->update();

        // $inventory = new Inventory();
        // $inventory->updateStock($invoice);

        return redirect(route('purchase-orders.show', $invoice->invoice_number))->with('success', 'order received'); 
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
