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
        //
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->renameColumn('ordered_at', 'blocked_at');
            $table->renameColumn('confirmed_at', 'booked_at');
            $table->renameColumn('shipped_at', 'dispatched_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->renameColumn('blocked_at', 'ordered_at');
            $table->renameColumn('booked_at', 'confirmed_at');
            $table->renameColumn('dispatched_at', 'shipped_at');
        });
    }
};
