<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryMovement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_movement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id');
            $table->foreignId('product_id');
            $table->string('model_name', 3);
            $table->integer('model_id');
            $table->integer('model_status');
            $table->integer('quantity');
            $table->integer('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_movement');
    }
}
