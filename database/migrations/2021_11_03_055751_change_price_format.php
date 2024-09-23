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
        Schema::table('purchase_order_invoice_items', function (Blueprint $table) {
            $table->decimal('selling_price', $precision = 13, $scale = 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('purchase_order_invoice_items', function (Blueprint $table) {
            $table->integer('selling_price')->change();
        });
    }
};
