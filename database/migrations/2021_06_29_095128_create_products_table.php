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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id');
            $table->foreignId('supplier_id');
            $table->foreignId('tax_id');
            $table->string('code')->unique();
            $table->string('description');
            $table->string('model');
            $table->string('cable_length');
            $table->string('kw_rating');
            $table->string('part_number');
            $table->text('notes');
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('category_id')->references('id')->on('category');
            // $table->foreign('supplier_id')->references('id')->on('supplier');
            // $table->foreign('tax_id')->references('id')->on('tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
