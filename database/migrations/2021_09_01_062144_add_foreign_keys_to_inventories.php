<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_item_id', 'warehouse_id', 'product_id']);
        });
    }
};
