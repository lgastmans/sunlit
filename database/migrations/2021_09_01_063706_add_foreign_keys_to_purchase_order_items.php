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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('tax_id')->references('id')->on('taxes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id', 'product_id', 'tax_id']);

        });
    }
};
