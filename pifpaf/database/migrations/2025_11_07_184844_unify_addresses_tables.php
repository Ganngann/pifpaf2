<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename the pickup_addresses table to addresses
        Schema::rename('pickup_addresses', 'addresses');

        // 2. Add the new columns to the addresses table
        Schema::table('addresses', function (Blueprint $table) {
            // Make the 'type' column nullable initially to avoid SQLite errors
            $table->string('type')->nullable()->after('user_id');
            $table->string('country')->nullable()->after('city');
        });

        // 3. Populate the 'type' column for existing records
        DB::table('addresses')->update(['type' => 'pickup']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('country');
        });

        Schema::rename('addresses', 'pickup_addresses');
    }
};
