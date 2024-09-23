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
    public function up(): void
    {
        //
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_item_id']);

            $table->dropColumn('purchase_order_item_id');
            $table->renameColumn('current_stock', 'stock_available')->nullable();
            $table->integer('stock_ordered')->nullable()->after('current_stock');
            $table->integer('stock_booked')->nullable()->after('current_stock');
            $table->decimal('landed_cost', $precision = 13, $scale = 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignId('purchase_order_item_id')->after('id');
            $table->renameColumn('stock_available', 'current_stock');
            $table->dropColumn('stock_ordered')->nullable();
            $table->dropColumn('stock_booked')->nullable();
            $table->integer('landed_cost')->change();

            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items');
        });
    }
};
