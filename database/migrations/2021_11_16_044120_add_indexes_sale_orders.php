<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
     */
    public function down(): void
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropIndex('sale_orders_order_number_index');
            $table->dropIndex('sale_orders_ordered_at_index');
            $table->dropIndex('sale_orders_due_at_index');
            $table->dropIndex('sale_orders_amount_index');
            $table->dropIndex('sale_orders_status_index');
        });
    }
};
