<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceInr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_invoice_items', function (Blueprint $table) {
            $table->decimal('selling_price_inr', $precision = 13, $scale = 2)->nullable();
            $table->decimal('paid_price_inr', $precision = 13, $scale = 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_invoice_items', function (Blueprint $table) {
            $table->dropColumn('selling_price_inr');
            $table->dropColumn('paid_price_inr');
        });
    }
}
