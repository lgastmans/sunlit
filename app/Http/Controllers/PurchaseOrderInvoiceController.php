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
        //
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
        

        return redirect(route('purchase-orders.show', $order->order_number)); 
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $order_number
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
        // $validated = $request->validate([
        //     'customs_at' => 'required|date',
        //     'boe_number' => 'required',
        // ]);
        // $order = PurchaseOrder::find($id);
        // $order->customs_at = $request->get('customs_at');
        // $order->boe_number = $request->get('boe_number');
        // $order->status = PurchaseOrder::CUSTOMS;
        // $order->update();
        // return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order at customs'); 
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
        // $validated = $request->validate([
        //     'cleared_at' => 'required|date',
        //     'customs_exchange_rate' => 'required',
            
        // ]);
        // $order = PurchaseOrder::find($id);
        // $order->cleared_at = $request->get('cleared_at');
        // $order->customs_exchange_rate = $request->get('customs_exchange_rate');
        // $order->customs_duty = $order->amount_inr_customs * \Setting::get('purchase_order.customs_duty') / 100;
        // $order->social_welfare_surcharge = $order->customs_duty * \Setting::get('purchase_order.social_welfare_surcharge') / 100;
        // $order->igst = ($order->amount_inr + $order->customs_duty + $order->social_welfare_surcharge )* \Setting::get('purchase_order.igst') / 100;
        // $order->bank_and_transport_charges = $order->amount_inr * \Setting::get('purchase_order.transport') / 100;
        // $charges = [
        //     'customs_duty'=> \Setting::get('purchase_order.customs_duty'),
        //     'social_welfare_surcharge'=> \Setting::get('purchase_order.social_welfare_surcharge'),
        //     'igst'=> \Setting::get('purchase_order.igst'),
        //     'transport'=> \Setting::get('purchase_order.transport'),
        // ];
        // $order->charges = json_encode($charges);
        // $order->status = PurchaseOrder::CLEARED;
        // $order->update();
        // return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order cleared'); 
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
        // $order = PurchaseOrder::find($id);
        // $order->received_at = $request->get('received_at');
        // $order->status = PurchaseOrder::RECEIVED;
        // $order->update();

        // $inventory = new Inventory();
        // $inventory->updateStock($order);

        // return redirect(route('purchase-orders.show', $order->order_number))->with('success', 'order received'); 
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
