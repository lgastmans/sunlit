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
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->smallInteger('movement_type')->default(1);
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
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn('movement_type');
        });
    }
};
