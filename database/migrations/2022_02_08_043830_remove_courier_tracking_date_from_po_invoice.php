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
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->dropColumn('tracking_number');
            $table->dropColumn('courier');
            $table->dropColumn('due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_invoices', function (Blueprint $table) {
            $table->date('due_at')->nullable()->after('status');
            $table->string('tracking_number')->nullable()->after('shipped_at');
            $table->string('courier', '150')->nullable()->after('tracking_number');

        });
    }
};
