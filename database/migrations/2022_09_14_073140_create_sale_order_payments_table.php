<?php

use App\Models\SaleOrder;
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
        Schema::create('sale_order_payments', function (Blueprint $table) {
            $table->id();
            //$table->foreignIdFor(SaleOrder::class);
            $table->foreignId('sale_order_id');
            $table->decimal('amount', $precision = 13, $scale = 2)->nullable();
            $table->string('reference')->nullable();
            $table->date('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sale_order_id')->references('id')->on('sale_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_order_payments');
    }
};
