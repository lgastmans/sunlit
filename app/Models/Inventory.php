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
        return $this->belongsTo(Product::class);
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

                $inventory->stock_ordered = $product->quantity_confirmed;

                $result = $inventory->update();
            }
        }

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
                            "quantity" => $product->quantity_shipped,
                            "user_id" => Auth::user()->id,
                            "movement_type" => InventoryMovement::RECEIVED,
                            "price" => $product->buying_price_inr
                        );
                        $movement = new InventoryMovement();
                        $movement->updateMovement($data);

                        $avg_price = $movement->getAverageBuyingPrice($inventory->warehouse_id, $product->product_id);
                    }

                    $result = $inventory->update([
                        "stock_available" => $available,
                        "average_buying_price" => $avg_price
                    ]);
                    //log price here

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

    /**
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
        if ($model->status == SaleOrder::DRAFT)
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
