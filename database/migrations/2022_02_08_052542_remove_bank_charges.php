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
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->dropColumn('bank_and_transport_charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->decimal('bank_and_transport_charges', $precision = 13, $scale = 2)->nullable()->after('amount_inr');
        });
    }
};
