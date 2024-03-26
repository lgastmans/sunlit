<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

//use App\Models\PurchaseOrder;
//use App\Models\SaleOrder;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'product_id', 'stock_available', 'stock_booked', 'stock_ordered', 'stock_blocked', 'average_buying_price', 'average_selling_price'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }


    /**
     * Create a new entry for each warehouse in the inventory for a given product
     * initializing the stock values to zero
     *
     * @param  int $warehouse_id
     * @param  int $product_id
     * @return boolean
     */
    public function initStock($product_id)
    {
        /*
            retrieve the ids of all warehouses
        */
        $warehouses = Warehouse::pluck('id');

        /*
            retrieve the product details
            for storing the initial average buying price
        */
        $product = Product::find($product_id);

        foreach ($warehouses as $warehouse_id) {
            $inventory = $this->create([
                "warehouse_id" => $warehouse_id,
                "product_id" => $product_id,
                "stock_available" => $this->stock_available,
                "stock_booked" => 0,
                "stock_ordered" => 0,
                "stock_blocked" => 0,
                "average_buying_price" => $product->purchase_price,
                "average_selling_price" => 0
            ]);
        }

        return $warehouses->first();
    }


    /**
     * Create a new entry in the inventory for a given warehouse_id
     * initializing the stock values to zero
     *
     * @param  int $warehouse_id
     * @param  int $product_id
     * @return boolean
     */
    public function initProductStock($warehouse_id, $product_id)
    {
        $current_inventory = $this->where('warehouse_id', '=', $warehouse_id)->where('product_id', '=', $product_id)->first();
        if ($current_inventory){
            return $current_inventory;
        }

        /*
            retrieve the product details
            for storing the initial average buying price
        */
        $product = Product::find($product_id);

        $inventory = $this->create([
            "warehouse_id" => $warehouse_id,
            "product_id" => $product_id,
            "stock_available" => 0,
            "stock_booked" => 0,
            "stock_ordered" => 0,
            "stock_blocked" => 0,
            "average_buying_price" => $product->purchase_price,
            "average_selling_price" => 0
        ]);
        if ($inventory) {
            return $this->find($inventory)->first();
        }
        return false;
    }


    /**
     * Update the Purchase Order stock ordered column in the inventory 
     *
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function updateOrderedStock(Model $model)
    {
        $order = PurchaseOrder::find($model->id);

        foreach($model->items as $product)
        {
            $inventory = $this->initProductStock($order->warehouse_id, $product->product_id);

            if ($inventory) {

                $inventory->warehouse_id = $order->warehouse_id;
                $inventory->product_id = $product->product_id;

                $inventory->stock_ordered += $product->quantity_confirmed;

                $result = $inventory->update();
            }
        }

    }


    /**
     * get the total stock ordered for a given product, warehouse
     * 
     */
    public function getStockOrdered($product_id, $warehouse_id)
    {
        /*
            SELECT poi.product_id, SUM(poi.quantity_confirmed)
            FROM `purchase_order_items` poi, warehouses w
            INNER JOIN purchase_orders po ON (po.warehouse_id = w.id)
            WHERE (poi.purchase_order_id = po.id) AND (po.status >=3)
            GROUP BY product_id        
        */
        $res = PurchaseOrderItem::select('product_id')
            ->selectRaw('SUM(purchase_order_items.quantity_confirmed) AS ordered_stock')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('warehouses', 'purchase_orders.warehouse_id', '=', 'warehouses.id')
            ->where('purchase_orders.warehouse_id', '=', $warehouse_id)
            ->where('purchase_orders.status', '>=', PurchaseOrder::CONFIRMED)
            ->where('purchase_order_items.product_id', '=', $product_id)
            ->groupBy('product_id')
            //->toSql();
            ->first();

        return $res;
    }


    /**
     * 
     * get the total stock received for a given product, warehouse
     * 
     */
    public function getStockReceived($product_id, $warehouse_id, $till_date=null)
    {
        /*
            SELECT pii.product_id, SUM(quantity_shipped)
            FROM `purchase_order_invoice_items` pii
            INNER JOIN purchase_order_invoices pis ON (pii.purchase_order_invoice_id = pis.id)
            INNER JOIN purchase_orders po ON (pis.purchase_order_id = po.id)
            WHERE (pis.status >= 7) AND (po.warehouse_id = 2) AND (pii.product_id = 616)
            GROUP BY product_id
        */

        if (is_null($till_date))
            $till_date = date('Y-m-d');

        /**
         * get stock received through Purchase Orders
         */
        $po = PurchaseOrderInvoiceItem::select('product_id')
            ->selectRaw('SUM(purchase_order_invoice_items.quantity_shipped) AS received_stock')
            ->join('purchase_order_invoices', 'purchase_order_invoice_items.purchase_order_invoice_id', '=', 'purchase_order_invoices.id')
            ->join('purchase_orders', 'purchase_order_invoices.purchase_order_id', '=', 'purchase_orders.id')
            ->where('purchase_order_invoices.status', '>=', PurchaseOrderInvoice::RECEIVED)
            ->where('purchase_orders.warehouse_id', '=', $warehouse_id)
            ->where('purchase_order_invoice_items.product_id', '=', $product_id)
            ->whereDate('purchase_order_invoices.received_at', '<=', $till_date)
            ->groupBy('product_id')
            //->toSql();
            ->first();
        
        $received_stock = 0;
        if ($po)
            $received_stock = $po['received_stock'];

        /**
         * get stock received through Credit Notes
         */
        $cn = CreditNoteItem::select('product_id')
            ->selectRaw('SUM(credit_note_items.quantity) AS received_stock')
            ->join('credit_notes', 'credit_notes.id', '=', 'credit_note_items.credit_note_id')
            ->where('credit_notes.status', '>=', CreditNote::CONFIRMED)
            ->where('credit_notes.warehouse_id', '=', $warehouse_id)
            ->where('credit_note_items.product_id', '=', $product_id)
            ->whereDate('credit_notes.confirmed_at', '<=', $till_date)
            ->groupBy('product_id')
            //->toSql();
            ->first();

        if ($cn)
            $received_stock += $cn['received_stock'];


        /**
         * The opening stock got initialized at the launch of the software
         * without creating an entry in the Purchase
         * This quantity is found in the Inventory Movement table, where
         * the entry will have all ids set to null, and this quantity
         * needs to be considered in the total received
         * 
         * see ProductController, function importCsv
         * 
         */
        $init_stock = InventoryMovement::select('quantity')
            ->where('warehouse_id', '=', $warehouse_id)
            ->where('product_id', '=', $product_id)
            ->whereNull('purchase_order_id')
            ->whereNull('purchase_order_invoice_id')
            ->whereNull('sales_order_id')
            ->whereNull('credit_note_id')
            ->first();

        $initial_stock = 0;
        if ($init_stock)
            $initial_stock = $init_stock['quantity'];

        return $received_stock + $initial_stock;
    }


    /**
     * get the total stock delivered for a given product, warehouse
     * 
     */
    public function getStockDispatched($product_id, $warehouse_id, $till_date=null)
    {
        if (is_null($till_date))
            $till_date = date('Y-m-d');

        $res = SaleOrderItem::select('product_id')
            ->selectRaw('SUM(sale_order_items.quantity_ordered) AS dispatched_stock')
            ->join('sale_orders', 'sale_order_items.sale_order_id', '=', 'sale_orders.id')
            ->where('sale_orders.status', '>=', SaleOrder::DISPATCHED)
            ->where('sale_orders.warehouse_id', '=', $warehouse_id)
            ->where('sale_order_items.product_id', '=', $product_id)
            ->whereDate('sale_orders.dispatched_at', '<=', $till_date)
            ->groupBy('product_id')
            //->toSql();
            ->first();

        $dispatched_stock = 0;
        if ($res)
            $dispatched_stock = $res['dispatched_stock'];

        return $dispatched_stock;
    }


    /**
     * Update the stock values in the inventory based on the model and related status
     * initializing the stock values to zero
     *
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function updateStock(Model $model)
    {
        /*
        *   get name of model
        */
        $class_name = class_basename($model);

        /*
        *   set the warehouse id of the model
        */
        $this->warehouse_id = $model->warehouse_id;

        /*
        *    iterate through the related items of the model
        *    and update the stock values
        */
        if ($class_name == 'PurchaseOrderInvoice')
        {
            $order = PurchaseOrder::find($model->purchase_order_id);

            foreach($model->items as $product)
            {
                $inventory = $this->initProductStock($order->warehouse_id, $product->product_id);

                if ($inventory){
                    // $inventory->first();

                    $inventory->warehouse_id = $order->warehouse_id;
                    $inventory->product_id = $product->product_id;

                    $ordered = $inventory->stock_ordered;
                    $available = $inventory->stock_available;

                    if ($model->status == PurchaseOrderInvoice::SHIPPED)
                    {
                        /*
                        *    update Ordered (add)
                        *    the Ordered quantity is set when the Purchase Order is Confirmed
                        */
                        //$ordered += $product->quantity_shipped;

                        /*
                            the average buying price stays the same
                        */
                        $avg_price = $inventory->average_buying_price;
                        
                    }
                    elseif ($model->status == PurchaseOrderInvoice::RECEIVED) 
                    {
                        /*
                        *    update Available Stock (add), update Ordered Stock (deduct)
                        */
                        $ordered -= $product->quantity_shipped;

                        $available += $product->quantity_shipped;

                        /*
                        *   register stock received in the Inventory Movement model
                        *   status RECEIVED
                        */
                        $data = array(
                            "warehouse_id" => $order->warehouse_id,
                            "product_id" => $product->product_id,
                            "purchase_order_id" => $model->purchase_order_id,
                            "purchase_order_invoice_id" => $model->id,
                            "sales_order_id" => null,
                            "credit_note_id" => null,
                            "quantity" => $product->quantity_shipped,
                            "user_id" => Auth::user()->id,
                            "movement_type" => InventoryMovement::RECEIVED,
                            "price" => $product->buying_price_inr
                        );
                        $movement = new InventoryMovement();
                        $movement->updateMovement($data);

                        $avg_price = $movement->getAverageBuyingPrice($inventory->warehouse_id, $product->product_id);
                    }
                    elseif ($model->status == PurchaseOrderInvoice::CANCELLED)
                    {
                        /**
                         *    update Available Stock (deduct), update Ordered Stock (add)
                         */
                        $ordered += $product->quantity_shipped;

                        $available -= $product->quantity_shipped;

                        /**
                         *   register stock received in the Inventory Movement model
                         *   status CANCELLED
                         */
                        $data = array(
                            "warehouse_id" => $order->warehouse_id,
                            "product_id" => $product->product_id,
                            "purchase_order_id" => $model->purchase_order_id,
                            "purchase_order_invoice_id" => $model->id,
                            "sales_order_id" => null,
                            "credit_note_id" => null,
                            "quantity" => $product->quantity_shipped,
                            "user_id" => Auth::user()->id,
                            "movement_type" => InventoryMovement::CANCELLED,
                            "price" => $product->buying_price_inr
                        );
                        $movement = new InventoryMovement();
                        $movement->updateMovement($data);

                        /**
                         * set the item in the purchase_order_invoice_items table as deleted
                         */
                        $product->delete();

                        /**
                         * the average buying price stays the same
                         */
                        $avg_price = $inventory->average_buying_price;                        
                    }

                    $result = $inventory->update([
                        "stock_available" => $available,
                        "stock_ordered" => $ordered,
                        "average_buying_price" => $avg_price
                    ]);
                    //log price here

                }
            }
        }
        elseif ($class_name == 'CreditNote')
        {
            foreach($model->items as $item)
            {
                $inventory = $this->initProductStock($model->warehouse_id, $item->product_id);

                if ($inventory){

                    //$inventory->warehouse_id = $model->warehouse_id;
                    //$inventory->product_id = $item->product_id;

                    $available = $inventory->stock_available + $item->quantity;

                    /*
                    *   register stock received in the Inventory Movement model
                    *   status RECEIVED
                    */
                    $data = array(
                        "warehouse_id" => $model->warehouse_id,
                        "product_id" => $item->product_id,
                        "purchase_order_id" => null,
                        "purchase_order_invoice_id" => null,
                        "sales_order_id" => null,
                        "credit_note_id" => $model->id,
                        "quantity" => $item->quantity,
                        "user_id" => Auth::user()->id,
                        "movement_type" => InventoryMovement::RECEIVED,
                        "price" => $item->price
                    );
                    $movement = new InventoryMovement();
                    $movement->updateMovement($data);                

                    $result = $inventory->update([
                        "warehouse_id" => $model->warehouse_id,
                        "product_id" => $item->product_id,
                        "stock_available" => $available
                    ]);
                }
            }
        }     
        elseif ($class_name == 'SaleOrder')
        {
            foreach($model->items as $product)
            {
                $inventory = $this->initProductStock($this->warehouse_id, $product->product_id);

                if ($inventory){

                    $inventory->warehouse_id = $model->warehouse_id;
                    $inventory->product_id = $product->product_id;

                    $booked = $inventory->stock_booked;
                    $available = $inventory->stock_available;
                    $blocked = $inventory->stock_blocked;
                    $avg_price = $inventory->average_selling_price;

                    if ($model->status == SaleOrder::BLOCKED)
                    {

                        $blocked += $product->quantity_ordered;

                    }
                    elseif ($model->status == SaleOrder::BOOKED)
                    {
                        /*
                        *    update Blocked Stock (deduct)
                        */
                        $blocked -= $product->quantity_ordered;

                        /*
                        *    update Booked Stock (add)
                        */
                        $booked += $product->quantity_ordered;

                    }
                    elseif ($model->status == SaleOrder::DISPATCHED) 
                    {
                        /*
                        *    update Available Stock (deduct), update Booked Stock (deduct)
                        */
                        $booked -= $product->quantity_ordered;
                        $available -= $product->quantity_ordered;

                        /*
                        *   register stock delivered in the Inventory Movement model
                        *   status DELIVERED
                        */
                        $data = array(
                            "warehouse_id" => $model->warehouse_id,
                            "product_id" => $product->product_id,
                            "purchase_order_id" => null,
                            "purchase_order_invoice_id" => null,
                            "sales_order_id" => $model->id,
                            "credit_note_id" => null,
                            "quantity" => $product->quantity_ordered,
                            "user_id" => Auth::user()->id,
                            "movement_type" => InventoryMovement::DELIVERED,
                            "price" => $product->selling_price
                        );
                        $movement = new InventoryMovement();
                        $movement->updateMovement($data);

                        $avg_price = $movement->getAverageSellingPrice($inventory->warehouse_id, $product->product_id);
                    }

                    $result = $inventory->update([
                        "stock_available" => $available,
                        "stock_booked" => $booked,
                        "stock_blocked" => $blocked,
                        "average_selling_price" => $avg_price
                    ]);

                }
            }

        }
    }


    public function updateItemStock(Model $model, $product_id, $update_quantity)
    {
        //$class_name = class_basename($model);
        //die($class_name.":".$model->warehouse_id.":".$product_id);

        // $inventory = $this->where('warehouse_id', '=', $model->warehouse_id)->where('product_id', '=', $product_id)->first();
        
        $inventory = $this->initProductStock($model->warehouse_id, $product_id);

        if ($inventory) {
            if ($model->status == SaleOrder::BLOCKED)
            {
                $inventory->stock_blocked += $update_quantity;

                $inventory->update();
            }
            elseif ($model->status == SaleOrder::BOOKED)
            {
                $inventory->stock_booked += $update_quantity;

                $inventory->update();
            }
        }
    }

    
    public function hasStock($warehouse_id, $product_id, $ordered)
    {
        $inventory = $this->where('warehouse_id', '=', $warehouse_id)->where('product_id', '=', $product_id)->first();

        if ($ordered > $inventory->stock_available)
            return false;

        return true;
    }


    /**
     * THIS FUNCTION IS OBSOLETE 
     * 
     * Update the blocked stock quantity in the inventory 
     * The model passed is assumed SaleOrderItem
     *
     * @param  Illuminate\Database\Eloquent\Model $model
     * @param  int $update_quantity
     * @return boolean
     */    
    public function updateStockBlocked(Model $model, $update_quantity)
    {
        $inventory = $this->initProductStock($model->sale_order->warehouse_id, $model->product_id);

        if ($model->sale_order->status == SaleOrder::DRAFT)
        {
            /*
            *    update Blocked Stock (add)
            */
            $blocked = $model->quantity_ordered + $update_quantity;

            $result = $inventory->update([
                "stock_blocked" => $blocked,
            ]);
        }
    }


    /**
     * Update the inventory
     * The related items 
     *
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */    
    public function deleteStock(Model $model)
    {
        /*
            const DRAFT = 1;
            const BLOCKED = 2;
            const BOOKED = 3;
        */

        if ($model->status == SaleOrder::DRAFT) // do nothing
        {

        }
        elseif ($model->status == SaleOrder::BLOCKED) 
        {
            foreach($model->items as $product)
            {
                $inventory = Inventory::where('warehouse_id', '=', $model->warehouse_id)
                    ->where('product_id', '=', $product->product_id)->first();

                if ($inventory)
                {
                    $result = $inventory->update([
                        "stock_blocked" => ($inventory->stock_blocked - $product->quantity_ordered)
                    ]);
                }
            }            
        }
        elseif ($model->status == SaleOrder::BOOKED) 
        {
            foreach($model->items as $product)
            {
                $inventory = Inventory::where('warehouse_id', '=', $model->warehouse_id)
                    ->where('product_id', '=', $product->product_id)->first();

                if ($inventory)
                {
                    $result = $inventory->update([
                        "stock_booked" => ($inventory->stock_booked - $product->quantity_ordered)
                    ]);
                }
            }            
        }
    }

    public static function getStockFilterList()
    {
        return [
            '__ALL_' => 'All', 
            '__BELOW_MIN_' => 'Below Minimum',
            '__NONE_ZERO_' => 'Non-Zero',
            '__ZERO_' => 'Zero'
        ];
    }

    public function scopeSearchProduct($query, $product_id)
    {
      if ($product_id) $query->where('product_id', $product_id);
    }

    public function scopeSearchWarehouse($query, $warehouse_id)
    {
      if ($warehouse_id) $query->where('warehouse_id', $warehouse_id);
    }
}
