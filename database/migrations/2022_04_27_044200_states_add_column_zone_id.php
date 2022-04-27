<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StatesAddColumnZoneId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('states', function (Blueprint $table) {
            $table->unsignedBigInteger('freight_zone_id')->after('id')->nullable();
            $table->foreign('freight_zone_id')->references('id')->on('freight_zones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('states', function (Blueprint $table) {
            $table->dropForeign('states_freight_zone_id_foreign');
            $table->dropColumn('freight_zone_id');
        });
    }
}
