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
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id');
            $table->string('order_number');
            $table->date('ordered_at')->nullable();
            $table->date('confirmed_at')->nullable();
            $table->date('shipped_at')->nullable();
            $table->date('received_at')->nullable();
            $table->date('due_at')->nullable();
            $table->date('paid_at')->nullable();
            $table->decimal('amount', $precision = 13, $scale = 2)->nullable();
            $table->decimal('transport_charges', $precision = 13, $scale = 2)->nullable();
            $table->string('tracking_number')->nullable();
            $table->date('courier')->nullable();
            $table->smallInteger('status')->default('1');
            $table->json('shipping_address')->nullable();
            $table->json('payment')->nullable();
            $table->foreignId('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dealer_id')->references('id')->on('dealers');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_orders');
    }
};
