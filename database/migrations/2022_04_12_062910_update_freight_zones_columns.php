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
    public function up()
    {
        //
        Schema::table('freight_zones', function (Blueprint $table) {
            $table->dropColumn('states');
            $table->decimal('rate_per_kg', 6, 2)->after('zone');
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
            $table->string('states');
            $table->dropColumn('rate_per_kg')->default(8);
        });
    }
};
