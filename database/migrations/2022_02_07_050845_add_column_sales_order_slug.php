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
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->string('order_number_slug')->after('order_number');
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
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropColumn('order_number_slug');
        });
    }
};
