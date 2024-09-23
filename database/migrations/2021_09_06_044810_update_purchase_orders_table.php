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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->renameColumn('expected_at', 'confirmed_at');
            $table->renameColumn('se_ex_rate', 'order_exchange_rate');
            $table->renameColumn('customs_ex_rate', 'customs_exchange_rate');
            $table->renameColumn('se_due_date', 'due_at');
            $table->date('shipped_at')->nullable();
            $table->string('tracking_number')->nullable();
            $table->date('courier')->nullable();
            $table->date('customs_at')->nullable();
            $table->dropColumn('credit_period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->renameColumn('confirmed_at', 'expected_at');
            $table->renameColumn('order_exchange_rate', 'se_ex_rate');
            $table->renameColumn('customs_exchange_rate', 'customs_ex_rate');
            $table->renameColumn('due_at', 'se_due_date');
            $table->dropColumn('shipped_at');
            $table->dropColumn('tracking_number');
            $table->dropColumn('courier');
            $table->dropColumn('customs_at');
            $table->integer('credit_period')->nullable();
        });
    }
};
