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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index('order_number');
            $table->index('ordered_at');
            $table->index('due_at');
            $table->index('amount_inr');
            $table->index('status');
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
            $table->dropIndex('purchase_orders_order_number_index');
            $table->dropIndex('purchase_orders_ordered_at_index');
            $table->dropIndex('purchase_orders_due_at_index');
            $table->dropIndex('purchase_orders_amount_inr_index');
            $table->dropIndex('purchase_orders_status_index');
        });

    }
};
