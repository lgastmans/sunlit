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
        //
        Schema::table('sale_order_payments', function (Blueprint $table) {
            $table->foreignId('dealer_id')->after('sale_order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('sale_order_payments', function (Blueprint $table) {
            $table->dropColumn('dealer_id');
        });
    }
};
