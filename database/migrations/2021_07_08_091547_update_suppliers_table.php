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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('address2')->nullable()->after('address');

            $table->foreignId('state_id')->nullable()->change();
            $table->string('gstin')->nullable()->change();
            $table->string('phone2')->nullable()->change();
            $table->string('contact_person')->nullable()->change();
            $table->string('email')->nullable()->change();

            $table->dropColumn('district');

            $table->renameColumn('company_title', 'company');
            $table->renameColumn('zip', 'zip_code');
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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('district');

            $table->renameColumn('company', 'company_title');
            $table->renameColumn('zip_code', 'zip');

            $table->dropColumn('address2');
        });
    }
};
