<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FreightZonesColumnChange extends Migration
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
            $table->dropColumn('zone');
            $table->string('name')->unique();
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
            // $table->dropUnique('name');
            $table->dropColumn('name');
            $table->string('zone')->unique();
        });
    }
}
