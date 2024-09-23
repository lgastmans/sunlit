<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'product_id', 'purchase_order_id', 'purchase_order_invoice_id', 'sales_order_id', 'credit_note_id', 'quantity', 'user_id', 'movement_type', 'price'];

    const RECEIVED = 1;

    const DELIVERED = 2;

    const CANCELLED = 3;

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase_order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * create a stock entry
     *
     * @return instance of object
     */
    public function updateMovement(array $data): instance
    {

        $movement = $this->create([
            'warehouse_id' => $data['warehouse_id'],
            'product_id' => $data['product_id'],
            'purchase_order_id' => $data['purchase_order_id'],
            'purchase_order_invoice_id' => $data['purchase_order_invoice_id'],
            'sales_order_id' => $data['sales_order_id'],
            'credit_note_id' => $data['credit_note_id'],
            'quantity' => $data['quantity'],
            'user_id' => $data['user_id'],
            'movement_type' => $data['movement_type'],
            'price' => $data['price'],
        ]);

        return $movement->fresh();
    }

    /**
     * calculate the average buying price
     *
     * @return average buying price
     */
    public static function getAverageBuyingPrice(int $warehouse_id, int $product_id): average
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
            //->where('purchase_order_invoice_id', '>', 0)
            ->first();

        return $query->average_price;
    }

    /**
     * calculate the average selling price
     *
     * @return average selling price
     */
    public function getAverageSellingPrice(int $warehouse_id, int $product_id): average
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
            InventoryMovement::DELIVERED => 'Out',
            InventoryMovement::CANCELLED => 'Cancelled',
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
        switch ($this->movement_type) {
            case InventoryMovement::RECEIVED:
                $status = '<h4><span class="badge badge-outline-primary">In</span></h4>';
                break;
            case InventoryMovement::DELIVERED:
                $status = '<h4><span class="badge badge-outline-secondary">Out</span></h4>';
                break;
            case InventoryMovement::CANCELLED:
                $status = '<h4><span class="badge badge-outline-warning">Cancelled</span></h4>';
                break;
            default:
                $status = '<h4><span class="badge badge-outline-error">Unknown</span></h4>';
        }

        return $status;
    }
}
