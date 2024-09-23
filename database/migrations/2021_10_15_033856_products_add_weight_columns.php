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
            $table->dropColumn('cable_length');
            $table->decimal('cable_length_input', $precision = 13, $scale = 2)->nullable();
            $table->decimal('cable_length_output', $precision = 13, $scale = 2)->nullable();
            $table->decimal('weight_actual', $precision = 13, $scale = 2)->nullable();
            $table->decimal('weight_volume', $precision = 13, $scale = 2)->nullable();
            $table->decimal('weight_calculated', $precision = 13, $scale = 2)->nullable();
            $table->smallInteger('warranty')->nullable();
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
            $table->string('cable_length')->nullable();
            $table->dropColumn('cable_length_input');
            $table->dropColumn('cable_length_output');
            $table->dropColumn('weight_actual');
            $table->dropColumn('weight_volume');
            $table->dropColumn('weight_calculated');
            $table->dropColumn('warranty');
        });
    }
};
