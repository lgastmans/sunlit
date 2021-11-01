<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id');
            $table->string('invoice_number');
            $table->smallInteger('status')->default('1');
            $table->date('due_at')->nullable();
            $table->decimal('order_exchange_rate', $precision = 13, $scale = 2)->nullable();
            $table->decimal('amount_usd', $precision = 13, $scale = 2)->nullable();
            $table->decimal('amount_inr', $precision = 13, $scale = 2)->nullable();
            
            $table->decimal('customs_duty', $precision = 13, $scale = 2)->nullable();
            $table->decimal('social_welfare_surcharge', $precision = 13, $scale = 2)->nullable();
            $table->decimal('igst', $precision = 13, $scale = 2)->nullable();
            $table->decimal('bank_and_transport_charges', $precision = 13, $scale = 2)->nullable();
            
            $table->date('shipped_at')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('courier','150')->nullable();

            $table->string('boe_number')->nullable();
            $table->decimal('customs_exchange_rate', $precision = 13, $scale = 2)->nullable();
            $table->date('customs_at')->nullable();

            $table->date('cleared_at')->nullable();
            
            $table->date('received_at')->nullable();

            $table->decimal('paid_exchange_rate', $precision = 13, $scale = 2)->nullable();
            $table->string('payment_reference')->nullable();
            $table->date('paid_at')->nullable();

            $table->json('charges')->nullable();

            $table->foreignId('user_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_invoices');
    }
}
