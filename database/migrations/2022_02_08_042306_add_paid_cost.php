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
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->decimal('landed_cost', $precision = 13, $scale = 2)->nullable()->after('cleared_at');
            $table->decimal('paid_cost', $precision = 13, $scale = 2)->nullable()->after('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->dropColumn('landed_cost');
            $table->dropColumn('paid_cost');
        });
    }
};
