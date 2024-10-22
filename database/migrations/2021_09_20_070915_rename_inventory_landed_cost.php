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
        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('landed_cost', 'average_cost')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('average_cost', 'landed_cost')->nullable();
        });
    }
};
