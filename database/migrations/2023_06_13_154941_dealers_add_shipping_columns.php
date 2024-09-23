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
        Schema::table('dealers', function (Blueprint $table) {
            $table->string('shipping_address3')->after('shipping_address2')->nullable();
            $table->string('shipping_contact_person2')->after('shipping_contact_person')->nullable();
            $table->string('shipping_email2')->after('shipping_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn('shipping_address3');
            $table->dropColumn('shipping_contact_person2');
            $table->dropColumn('shipping_email2');
        });
    }
};
