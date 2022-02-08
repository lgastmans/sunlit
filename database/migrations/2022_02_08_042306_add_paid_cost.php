<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->decimal('landed_cost', $precision = 13, $scale = 2)->nullable()->after('cleared_at');
            $table->decimal('paid_cost', $precision = 13, $scale = 2)->nullable()->after('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->dropColumn('landed_cost');
            $table->dropColumn('paid_cost');
        });
    }
}
