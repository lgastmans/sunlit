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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id');
            $table->foreignId('supplier_id');
            $table->string('order_number');
            $table->string('boe_number')->nullable();
            $table->date('ordered_at')->nullable();
            $table->date('expected_at')->nullable();
            $table->date('received_at')->nullable();
            $table->integer('credit_period')->nullable();
            $table->integer('amount_usd')->nullable();
            $table->integer('amount_inr')->nullable();
            $table->integer('customs_ex_rate')->nullable();
            $table->integer('se_ex_rate')->nullable();
            $table->integer('duty_amount')->nullable();
            $table->integer('social_surcharge')->nullable();
            $table->integer('igst')->nullable();
            $table->integer('bank_charges')->nullable();
            $table->integer('clearing_charges')->nullable();
            $table->integer('transport_charges')->nullable();
            $table->date('se_due_date')->nullable();
            $table->date('se_payment_date')->nullable();
            $table->smallInteger('status')->default('1');
            $table->integer('user_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
