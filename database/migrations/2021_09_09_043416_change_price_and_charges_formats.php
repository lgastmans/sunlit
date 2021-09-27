<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePriceAndChargesFormats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('amount_usd', $precision = 13, $scale = 2)->change();
            $table->decimal('amount_inr', $precision = 13, $scale = 2)->change();
            $table->decimal('customs_exchange_rate', $precision = 13, $scale = 2)->change();
            $table->decimal('order_exchange_rate', $precision = 13, $scale = 2)->change();
            $table->decimal('duty_amount', $precision = 13, $scale = 2)->change();
            $table->decimal('social_surcharge', $precision = 13, $scale = 2)->change();
            $table->decimal('igst', $precision = 13, $scale = 2)->change();
            $table->decimal('bank_charges', $precision = 13, $scale = 2)->change();
            $table->decimal('clearing_charges', $precision = 13, $scale = 2)->change();
            $table->decimal('transport_charges', $precision = 13, $scale = 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->integer('amount_usd')->change();
            $table->integer('amount_inr')->change();
            $table->integer('customs_exchange_rate')->change();
            $table->integer('order_exchange_rate')->change();
            $table->integer('duty_amount')->change();
            $table->integer('social_surcharge')->change();
            $table->integer('igst')->change();
            $table->integer('bank_charges')->change();
            $table->integer('clearing_charges')->change();
            $table->integer('transport_charges')->change();
        });
    }
}
