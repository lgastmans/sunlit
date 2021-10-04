<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['state_id', 'name', 'address', 'city', 'zip_code', 'contact_person', 'phone', 'phone2', 'email'];
    protected $with = ['state'];

    /**
     * Get the state associated with the warehouse.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    
    public function purchase_orders()
    {
        return $this->hasMany(PurchaseOrder::class);
    } 
    
    public function sale_orders()
    {
        return $this->hasMany(SaleOrder::class);
    }  

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }  

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }  
}
