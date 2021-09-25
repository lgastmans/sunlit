<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = ["warehouse_id", "product_id", "purchase_order_id", "sales_order_id", "quantity", "user_id", "movement_type"];

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
            "sales_order_id" => $data['sales_order_id'],
            "quantity" => $data['quantity'],
            "user_id" => $data['user_id'],
            "movement_type" => $data['movement_type']
        ]);

        return $movement->fresh();
    }

    /**
     * Returns the due_at date for display Month Day, Year
     */
    public function getDisplayCreatedAtAttribute()
    {
        $dt = Carbon::parse($this->created_at);
        return $this->attributes['created_at'] = $dt->toFormattedDateString();  
    }    

}
