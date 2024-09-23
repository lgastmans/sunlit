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
        Schema::table('freight_zones', function (Blueprint $table) {
            // $table->dropUnique('zone');
            $table->dropColumn('zone');
            $table->string('name')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('freight_zones', function (Blueprint $table) {
            // $table->dropUnique('name');
            $table->dropColumn('name');
            $table->string('zone')->unique();
        });
    }
};
