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
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->text('payment_terms')->after('charges')->nullable();
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
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->dropColumn('payment_terms');
        });
    }
};
