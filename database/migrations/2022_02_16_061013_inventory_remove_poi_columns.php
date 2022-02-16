<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InventoryRemovePoiColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['stock_poi_received', 'stock_poi_shipped']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('inventories', function (Blueprint $table) {
            $table->integer('stock_poi_shipped')->nullable();
            $table->integer('stock_poi_received')->nullable();
        });         
    }
}
