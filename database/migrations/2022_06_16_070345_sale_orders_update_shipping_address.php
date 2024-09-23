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
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->foreignId('shipping_state_id')->after('payment')->nullable();
            $table->string('shipping_company')->after('payment')->nullable();
            $table->string('shipping_address')->after('payment')->nullable();
            $table->string('shipping_address2')->after('payment')->nullable();
            $table->string('shipping_city')->after('payment')->nullable();
            $table->string('shipping_zip_code')->after('payment')->nullable();
            $table->string('shipping_gstin')->after('payment')->nullable();
            $table->string('shipping_contact_person')->after('payment')->nullable();
            $table->string('shipping_phone')->after('payment')->nullable();
        });

        Schema::table('sale_orders', function (Blueprint $table) {
            $table->foreign('shipping_state_id')->references('id')->on('states');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropForeign('sale_orders_shipping_state_id_foreign');
        });

        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropColumn('shipping_state_id');
            $table->dropColumn('shipping_company');
            $table->dropColumn('shipping_address');
            $table->dropColumn('shipping_address2');
            $table->dropColumn('shipping_city');
            $table->dropColumn('shipping_zip_code');
            $table->dropColumn('shipping_gstin');
            $table->dropColumn('shipping_contact_person');
            $table->dropColumn('shipping_phone');
        });

    }
};
