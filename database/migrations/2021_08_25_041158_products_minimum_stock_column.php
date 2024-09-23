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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('purchase_price')->after('model')->nullable();
            $table->integer('minimum_quantity')->after('model')->default(0);
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
            $table->dropColumn('minimum_quantity');
        });
    }
};
