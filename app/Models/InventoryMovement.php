<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = ["warehouse_id", "product_id", "purchase_order_id", "purchase_order_invoice_id", "sales_order_id", "quantity", "user_id", "movement_type", "price"];

    const RECEIVED = 1;
    const DELIVERED = 2;


    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * create a stock entry
     *
     * @param  array $data
     * @return instance of object
     */
    public function updateMovement($data)
    {

        $movement = $this->create([
            "warehouse_id" => $data['warehouse_id'],
            "product_id" => $data['product_id'],
            "purchase_order_id" => $data['purchase_order_id'],
            "purchase_order_invoice_id" => $data['purchase_order_invoice_id'],
            "sales_order_id" => $data['sales_order_id'],
            "quantity" => $data['quantity'],
            "user_id" => $data['user_id'],
            "movement_type" => $data['movement_type'],
            "price" => $data['price']
        ]);

        return $movement->fresh();
    }

    /**
     * calculate the average buying price
     *
     * @param int $warehouse_id
     * @param int $product_id
     * @return average buying price
     */
    public static function getAverageBuyingPrice($warehouse_id, $product_id)
    {
        /*
            SELECT (SUM(price * quantity) / SUM(quantity)) AS average_price 
            FROM `inventory_movements` 
            WHERE warehouse_id = 1 AND product_id = 311 AND purchase_order_id IS NOT NULL  
        */
        $query = InventoryMovement::selectRaw('(SUM(price * quantity) / SUM(quantity)) AS average_price ')
            ->where('warehouse_id', $warehouse_id)
            ->where('product_id', $product_id)
            ->whereNotNull('purchase_order_invoice_id')
            ->first();

        return $query->average_price;
    }

    /**
     * calculate the average selling price
     *
     * @param int $warehouse_id
     * @param int $product_id
     * @return average selling price
     */
    public function getAverageSellingPrice($warehouse_id, $product_id)
    {
        /*
            SELECT (SUM(price * quantity) / SUM(quantity)) AS average_price 
            FROM `inventory_movements` 
            WHERE warehouse_id = 1 AND product_id = 311 AND sales_order_id IS NOT NULL  
        */
        $query = InventoryMovement::selectRaw('(SUM(price * quantity) / SUM(quantity)) AS average_price ')
            ->where('warehouse_id', $warehouse_id)
            ->where('product_id', $product_id)
            ->whereNotNull('sales_order_id')
            ->first();

        return $query->average_price;
    }

    public static function getMovementFilterList()
    {
        return [
            0 => 'All', 
            InventoryMovement::RECEIVED => 'In',
            InventoryMovement::DELIVERED => 'Out'
        ];
    }


    /**
     * Returns the created_at date for display Month Day, Year
     */
    public function getDisplayCreatedAtAttribute()
    {
        $dt = Carbon::parse($this->created_at);
        return $this->attributes['created_at'] = $dt->toFormattedDateString();  
    }    


    public function getDisplayMovementTypeAttribute()
    {
        switch ($this->movement_type)
        {
            case InventoryMovement::RECEIVED:
                $status =  '<h4><span class="badge badge-outline-primary">In</span></h4>';
                break;
            case InventoryMovement::DELIVERED:
                $status = '<h4><span class="badge badge-outline-secondary">Out</span></h4>';
                break;
            default:
                $status = '<h4><span class="badge badge-outline-error">Unknown</span></h4>';
        }
        return $status;
    }

}
