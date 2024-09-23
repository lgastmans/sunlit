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
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->string('tcs_text', 124)->after('payment_terms')->nullable();
            $table->decimal('tcs', 8, 2)->after('payment_terms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropColumn('tcs_text');
            $table->dropColumn('tcs');
        });
    }
};
