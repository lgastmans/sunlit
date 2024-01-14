<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InventoryMovementAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->integer('credit_note_order_id')->nullable()->after('sales_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn('credit_note_order_id');
        });
    }
}
