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
            $table->dropColumn(['stock_poi_received', 'stock_poi_shipped']);
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
            $table->integer('stock_poi_shipped')->nullable();
            $table->integer('stock_poi_received')->nullable();
        });
    }
};
