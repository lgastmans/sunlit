<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained();
            $table->string('quotation_number');
            $table->string('quotation_slug_number');
            $table->date('confirmed_at')->nullable();
            $table->decimal('amount', $precision = 13, $scale = 2)->nullable();
            $table->decimal('transport_charges', $precision = 13, $scale = 2)->nullable();
            $table->date('courier')->nullable();
            $table->smallInteger('status')->default('1');
            $table->foreignId('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dealer_id')->references('id')->on('dealers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->index('quotation_number');
            $table->index('confirmed_at');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
