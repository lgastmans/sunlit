<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SaleOrderPaymentsAddColumnDealerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sale_order_payments', function (Blueprint $table) {
            $table->foreignId('dealer_id')->after('sale_order_id')->nullable();
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
        Schema::table('sale_order_payments', function (Blueprint $table) {
            $table->dropColumn('dealer_id');
        });
    }
}
