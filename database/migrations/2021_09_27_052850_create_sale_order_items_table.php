<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_order_id');
            $table->foreignId('product_id');
            $table->integer('quantity_ordered');
            $table->decimal('price', $precision = 13, $scale = 2);
            $table->decimal('tax', $precision = 13, $scale = 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sale_order_id')->references('id')->on('sale_orders');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_order_items');
    }
};
