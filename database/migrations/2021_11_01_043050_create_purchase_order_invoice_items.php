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
    public function up()
    {
        Schema::create('purchase_order_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_invoice_id');
            $table->foreignId('product_id');
            $table->integer('quantity_shipped');
            $table->integer('selling_price');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('purchase_order_invoice_id')->references('id')->on('purchase_order_invoices');
            $table->foreign('product_id')->references('id')->on('products');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_invoice_items');
    }
};
