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
        Schema::table('taxes', function (Blueprint $table) {
            $table->index('name');
            $table->index('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->dropIndex('taxes_name_index');
            $table->dropIndex('taxes_amount_index');
        });
    }
};
