<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FreightZonesPropertiesColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('freight_zones', function (Blueprint $table) {
            // $table->dropUnique('zone');
            $table->json('properties');
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
        Schema::table('freight_zones', function (Blueprint $table) {
            // $table->dropUnique('zone');
            $table->dropColumn('properties');
        });
    }
}
