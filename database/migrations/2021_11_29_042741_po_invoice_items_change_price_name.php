<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PoInvoiceItemsChangePriceName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_invoice_items', function (Blueprint $table) {
            $table->renameColumn('selling_price', 'buying_price');
            $table->renameColumn('selling_price_inr', 'buying_price_inr');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_invoice_items', function (Blueprint $table) {
            $table->renameColumn('buying_price', 'selling_price');
            $table->renameColumn('buying_price_inr', 'selling_price_inr');

        });
    }
}
