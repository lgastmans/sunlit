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
        Schema::table('states', function (Blueprint $table) {
            $table->unsignedBigInteger('freight_zone_id')->after('id')->nullable();
            $table->foreign('freight_zone_id')->references('id')->on('freight_zones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('states', function (Blueprint $table) {
            $table->dropForeign('states_freight_zone_id_foreign');
            $table->dropColumn('freight_zone_id');
        });
    }
};
