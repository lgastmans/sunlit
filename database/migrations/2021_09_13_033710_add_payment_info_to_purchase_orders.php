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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->renameColumn('se_payment_date', 'paid_at');
            $table->string('payment_reference');
            $table->renameColumn('bank_charges', 'bank_and_transport_charges');
            $table->renameColumn('duty_amount', 'customs_duty');
            $table->renameColumn('social_surcharge', 'social_welfare_surchage');
            $table->dropColumn('clearing_charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->renameColumn('paid_at', 'se_payment_date');
            $table->dropColumn('payment_reference');
            $table->renameColumn('bank_and_transport_charges', 'bank_charges');
            $table->renameColumn('customs_duty', 'duty_amount');
            $table->renameColumn('social_welfare_surchage', 'social_surcharge');
            $table->integer('clearing_charges')->nullable();
        });
    }
};
