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
        //

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('tax_id')->references('id')->on('taxes');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreign('state_id')->references('id')->on('states');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_category_id_foreign');
            $table->dropForeign('products_supplier_id_foreign');
            $table->dropForeign('products_tax_id_foreign');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign('suppliers_state_id_foreign');
        });
    }
};
