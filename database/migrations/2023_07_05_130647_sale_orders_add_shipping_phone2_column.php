<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SaleOrdersAddShippingPhone2Column extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->string('shipping_phone2')->after('shipping_phone')->nullable();
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
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropColumn('shipping_phone2');
        });        
    }
}
