<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SaleOrdersChangeCourierType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->string('courier','150')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->date('courier')->change();
        });
    }
}
