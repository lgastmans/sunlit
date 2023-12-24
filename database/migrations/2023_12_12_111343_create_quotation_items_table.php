<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id');
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->decimal('price', $precision = 13, $scale = 2);
            $table->decimal('tax', $precision = 13, $scale = 2);
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('quotation_id')->references('id')->on('quotations');
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
        Schema::dropIfExists('quotation_items');
    }
}
