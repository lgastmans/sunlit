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

    protected $fillable = ['warehouse_id', 'product_id', 'stock_available', 'stock_booked', 'stock_ordered'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Create a new entry in the inventory
     * initializing the stock values to zero
     *
     * @param  int $warehouse_id
     * @param  int $product_id
     * @return boolean
     */
    public function initStock($warehouse_id, $product_id)
    {
        // $current_inventory = $this->where('warehouse_id', '=', $warehouse_id)->where('product_id', '=', $product_id)->first();
        // if ($current_inventory){
        //     return $current_inventory;
        // }
        // $inventory = $this->create([
        //             "warehouse_id" => $warehouse_id,
        //             "product_id" => $product_id,
        //             "stock_available" => 0,
        //             "stock_booked" => 0,
        //             "stock_ordered" => 0
        // ]);
        // if ($inventory) {
        //     return $this->find($inventory);
        // }
        // return false;
        $search = $this->searchWarehouse($warehouse_id)->searchProduct($product_id)->get();

        if ($search->isEmpty())
        {
            $inventory = $this->create([
                "warehouse_id" => $warehouse_id,
                "product_id" => $product_id,
                "stock_available" => 0,
                "stock_booked" => 0,
                "stock_ordered" => 0
            ]);
            if ($inventory) {
                return $this->searchWarehouse($warehouse_id)->searchProduct($product_id)->get();
            }
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
                
                // $inventory = $this->initStock($this->warehouse_id, $this->product_id);
                // if ($inventory){
                $search = $this->searchWarehouse($this->warehouse_id)->searchProduct($product->product_id)->get();
                
                if ($search->isEmpty())
                    $search = $this->initStock($this->warehouse_id, $product->product_id);

                if ($search->count() > 0)
                {

                    $inventory = $search->first();

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
                            "movement_type" => InventoryMovement::RECEIVED
                        );
                        $movement = new InventoryMovement();
                        $movement->updateMovement($data);
                    }

                    $result = $inventory->update([
                        "stock_available" => $available,
                        "stock_ordered" => $ordered
                    ]);

                }
            }
        }
        elseif ($class_name == 'SalesOrder')
        {

        }

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
