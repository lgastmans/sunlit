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
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->renameColumn('received_at', 'delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->renameColumn('delivered_at', 'received_at');
        });
    }
};
