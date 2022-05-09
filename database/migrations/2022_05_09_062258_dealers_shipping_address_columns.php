<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DealersShippingAddressColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('dealers', function (Blueprint $table) {
            $table->boolean('has_shipping_address')->after('email')->default(true);
            $table->foreignId('shipping_state_id')->after('email')->nullable();
            $table->string('shipping_company')->after('email')->nullable();
            $table->string('shipping_address')->after('email')->nullable();
            $table->string('shipping_address2')->after('email')->nullable();
            $table->string('shipping_city')->after('email')->nullable();
            $table->string('shipping_zip_code')->after('email')->nullable();
            $table->string('shipping_gstin')->after('email')->nullable();
            $table->string('shipping_contact_person')->after('email')->nullable();
            $table->string('shipping_phone')->after('email')->nullable();
            $table->string('shipping_phone2')->after('email')->nullable();
            $table->string('shipping_email')->after('email')->nullable();
        });

        Schema::table('dealers', function (Blueprint $table) {
            $table->foreign('shipping_state_id')->references('id')->on('states');
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
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropForeign('dealers_shipping_state_id_foreign');
        });

        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn('has_shipping_address');
            $table->dropColumn('shipping_state_id');
            $table->dropColumn('shipping_company');
            $table->dropColumn('shipping_address');
            $table->dropColumn('shipping_address2');
            $table->dropColumn('shipping_city');
            $table->dropColumn('shipping_zip_code');
            $table->dropColumn('shipping_gstin');
            $table->dropColumn('shipping_contact_person');
            $table->dropColumn('shipping_phone');
            $table->dropColumn('shipping_phone2');
            $table->dropColumn('shipping_email');
        });
    }
}
