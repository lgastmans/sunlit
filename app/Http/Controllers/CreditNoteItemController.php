<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CreditNoteItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $product = Product::find($request->get('product_id'));

        if ($product) {

            $item = CreditNoteItem::where('credit_note_id', '=', $request->credit_id)->where('product_id', '=', $request->product_id)->first();

            $order = CreditNote::find($request->credit_note_id);

            if ($item) {
                $item->quantity = $request->quantity;
                $item->update();
            } else {
                $item = new CreditNoteItem;
                $item->credit_note_id = $request->credit_note_id;
                $item->product_id = $request->product_id;
                $item->tax = $product->tax->amount;
                $item->quantity = $request->quantity;
                $item->price = $request->price;
                $item->save();
            }

            return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK', 'item' => $item, 'product' => $product]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\QuotationItems  $quotationItems
     */
    public function update(Request $request, $id): JsonResponse
    {
        $item = CreditNoteItem::find($id);

        $order = CreditNote::find($item->credit_note_id);

        $update_quantity = 0;

        if ($request->field == 'quantity') {

            if ($order->status == CreditNote::DRAFT) {
                $item->quantity = $request->value;
            } else {
                $update_quantity = $request->value - $item->quantity;

                $item->quantity = $request->value;
            }
        }

        if ($request->field == 'price') {
            $item->price = $request->value;
        }

        $item->update();

        return response()->json(['success' => 'true', 'code' => 200, 'message' => 'OK']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QuotationItems  $quotationItems
     */
    public function destroy($id): RedirectResponse
    {
        $item = CreditNoteItem::find($id);

        $order = CreditNote::find($item->credit_note_id);

        CreditNoteItem::destroy($id);

        $order = CreditNote::find($item->credit_note_id);
        $order->amount = 0;
        foreach ($order->items as $item) {
            $order->amount += $item->price;
        }
        $order->update();

        return redirect(route('credit-notes.show', $order->credit_note_number_slug))->with('success', trans('app.record_deleted', ['field' => 'item']));
    }
}
