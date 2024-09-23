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
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealer_id')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained();
            $table->string('credit_note_number');
            $table->string('credit_note_slug_number');
            $table->date('confirmed_at')->nullable();
            $table->decimal('amount', $precision = 13, $scale = 2)->nullable();
            $table->date('courier')->nullable();
            $table->smallInteger('status')->default('1');
            $table->foreignId('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dealer_id')->references('id')->on('dealers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->index('credit_note_number');
            $table->index('confirmed_at');
        });

        Schema::create('credit_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_note_id');
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->decimal('price', $precision = 13, $scale = 2);
            $table->decimal('tax', $precision = 13, $scale = 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('credit_note_id')->references('id')->on('credit_notes');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
        Schema::dropIfExists('credit_note_items');
    }
};
