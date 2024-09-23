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
        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('average_cost', 'average_selling_price')->nullable();
            $table->decimal('average_buying_price', $precision = 13, $scale = 2)->nullable();
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
        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('average_selling_price', 'average_cost');
            $table->dropColumn('average_buying_price');
        });
    }
};
