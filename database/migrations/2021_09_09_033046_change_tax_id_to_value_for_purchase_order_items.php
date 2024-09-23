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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropForeign('purchase_order_items_tax_id_foreign');
            $table->dropColumn('tax_id');
            $table->integer('tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->foreignId('tax_id')->constrained();
            $table->dropColumn('tax');

        });
    }
};
