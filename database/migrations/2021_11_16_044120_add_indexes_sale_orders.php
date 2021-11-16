<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesSaleOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->index('order_number');
            $table->index('ordered_at');
            $table->index('due_at');
            $table->index('amount');
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
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropIndex('sale_orders_order_number_index');
            $table->dropIndex('sale_orders_ordered_at_index');
            $table->dropIndex('sale_orders_due_at_index');
            $table->dropIndex('sale_orders_amount_index');
            $table->dropIndex('sale_orders_status_index');
        });
    }
}
