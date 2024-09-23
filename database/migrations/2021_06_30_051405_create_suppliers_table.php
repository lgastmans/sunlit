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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id');
            $table->string('company_title');
            $table->string('address');
            $table->string('city');
            $table->string('zip');
            $table->string('district');
            $table->string('gstin');
            $table->string('contact_person');
            $table->string('phone');
            $table->string('phone2');
            $table->string('email');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
};
