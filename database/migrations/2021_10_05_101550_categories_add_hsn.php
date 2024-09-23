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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('hsn_code')->after('name')->nullable();
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
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('hsn_code');
        });
    }
};
