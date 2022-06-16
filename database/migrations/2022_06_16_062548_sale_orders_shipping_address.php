<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SaleOrdersShippingAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropColumn('shipping_address');
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

        Schema::table('sale_orders', function (Blueprint $table) {
            $table->json('shipping_address')->nullable();        
        });

        /*
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropForeign('dealers_shipping_state_id_foreign');
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
        */
    }
}
