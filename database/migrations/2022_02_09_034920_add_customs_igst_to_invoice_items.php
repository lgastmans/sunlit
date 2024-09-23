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
        Schema::table('purchase_order_invoice_items', function (Blueprint $table) {
            $table->json('charges')->nullable();
            $table->decimal('customs_duty', $precision = 13, $scale = 2)->nullable();
            $table->decimal('social_welfare_surcharge', $precision = 13, $scale = 2)->nullable();
            $table->decimal('igst', $precision = 13, $scale = 2)->nullable();
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
            $table->dropColumn('charges');
            $table->dropColumn('customs_duty');
            $table->dropColumn('social_welfare_surcharge');
            $table->dropColumn('igst');
        });
    }
};
