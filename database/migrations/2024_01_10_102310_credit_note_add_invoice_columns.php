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
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->string('invoice_number');
            $table->date('invoice_date')->nullable();
            $table->boolean('is_against_invoice')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
            $table->dropColumn('invoice_date');
            $table->dropColumn('is_against_invoice');
        });
    }
};
