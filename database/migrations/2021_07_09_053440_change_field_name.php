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
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('description', 'name');
        });
        Schema::table('taxes', function (Blueprint $table) {
            $table->renameColumn('description', 'name');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('description', 'name');
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
            $table->renameColumn('name', 'description');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('name', 'description');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name', 'description');
        });
    }
};
