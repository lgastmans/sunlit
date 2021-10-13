<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

//use App\Models\PurchaseOrder;
//use App\Models\SalesOrder;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'product_id', 'stock_available', 'stock_booked', 'stock_ordered', 'average_buying_price', 'average_selling_price'];

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
                "stock_available" => 0,
                "stock_booked" => 0,
                "stock_ordered" => 0,
                "average_buying_price" => $product->purchase_price,
                "average_selling_price" => 0
            ]);
        }
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
        $inventory = $this->create([
            "warehouse_id" => $warehouse_id,
            "product_id" => $product_id,
            "stock_available" => 0,
            "stock_booked" => 0,
            "stock_ordered" => 0
        ]);
        if ($inventory) {
            return $this->find($inventory)->first();
        }
        return false;
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
        if ($class_name == 'PurchaseOrder')
        {
            foreach($model->items as $product)
            {
                $inventory = $this->initProductStock($this->warehouse_id, $product->product_id);

                if ($inventory){
                    // $inventory->first();

                    $inventory->warehouse_id = $model->warehouse_id;
                    $inventory->product_id = $product->product_id;

                    $ordered = $inventory->stock_ordered;
                    $available = $inventory->stock_available;

                    if ($model->status == PurchaseOrder::CONFIRMED)
                    {
                        /*
                        *    update Ordered Stock (add)
                        */
                        $ordered += $product->quantity_ordered;
                        
                    }
                    elseif ($model->status == PurchaseOrder::RECEIVED) 
                    {
                        /*
                        *    update Available Stock (add), update Ordered Stock (deduct)
                        */
                        $ordered -= $product->quantity_ordered;
                        $available += $product->quantity_ordered;

                        /*
                        *   register stock received in the Inventory Movement model
                        *   status RECEIVED
                        */
                        $data = array(
                            "warehouse_id" => $model->warehouse_id,
                            "product_id" => $product->product_id,
                            "purchase_order_id" => $model->id,
                            "sales_order_id" => null,
                            "quantity" => $product->quantity_ordered,
                            "user_id" => Auth::user()->id,
                            "movement_type" => InventoryMovement::RECEIVED,
                            "price" => $product->selling_price
                        );
                        $movement = new InventoryMovement();
                        $movement->updateMovement($data);

                        $avg_price = $movement->getAverageBuyingPrice($inventory->warehouse_id, $product->product_id);
                    }

                    $result = $inventory->update([
                        "stock_available" => $available,
                        "stock_ordered" => $ordered,
                        "average_buying_price" => $avg_price
                    ]);

                }
            }
        }
        elseif ($class_name == 'SalesOrder')
        {
            foreach($model->items as $product)
            {
                $inventory = $this->initProductStock($this->warehouse_id, $product->product_id);

                if ($inventory){

                    $inventory->warehouse_id = $model->warehouse_id;
                    $inventory->product_id = $product->product_id;

                    $booked = $inventory->stock_booked;
                    $available = $inventory->stock_available;

                    if ($model->status == SaleOrder::CONFIRMED)
                    {
                        /*
                        *    update Booked Stock (add)
                        */
                        $ordered += $product->quantity_ordered;
                        
                    }
                    elseif ($model->status == SaleOrder::DELIVERED) 
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
                            "sales_order_id" => $model->id,
                            "quantity" => $product->quantity_ordered,
                            "user_id" => Auth::user()->id,
                            "movement_type" => InventoryMovement::DELIVERED,
                            "price" => $product->selling_price
                        );
                        $movement = new InventoryMovement();
                        $movement->updateMovement($data);

                        $avg_price = $movement->getAverageBuyingPrice($inventory->warehouse_id, $product->product_id);
                    }

                    $result = $inventory->update([
                        "stock_available" => $available,
                        "stock_booked" => $booked,
                        "average_selling_price" => $avg_price
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
