<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SaleOrdersAddPaymentTerms extends Migration
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
            $table->text('payment_terms')->after('payment')->nullable();
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
            $table->dropColumn('payment_terms');
        });
    }
}
