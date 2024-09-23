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
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->nullable();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('zip_code');
            $table->string('contact_person')->nullable();
            $table->string('phone');
            $table->string('phone2')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->foreign('state_id')->references('id')->on('states');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropForeign('warehouses_state_id_foreign');
        });

        Schema::dropIfExists('warehouses');
    }
};
