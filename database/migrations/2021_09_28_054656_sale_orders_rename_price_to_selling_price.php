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
        Schema::table('sale_order_items', function (Blueprint $table) {
            $table->renameColumn('price', 'selling_price');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('sale_order_items', function (Blueprint $table) {
            $table->renameColumn('selling_price', 'price');

        });
    }
};
