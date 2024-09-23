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
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('customs_duty', $precision = 13, $scale = 2)->nullable()->after('hsn_code');
            $table->decimal('social_welfare_surcharge', $precision = 13, $scale = 2)->nullable()->after('customs_duty');
            $table->decimal('igst', $precision = 13, $scale = 2)->nullable()->after('social_welfare_surcharge');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('customs_duty');
            $table->dropColumn('social_welfare_surcharge');
            $table->dropColumn('igst');
        });
    }
};
