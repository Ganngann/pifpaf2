<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean up any orphaned address_id records to prevent integrity issues.
        DB::statement('UPDATE transactions SET address_id = NULL WHERE address_id IS NOT NULL AND address_id NOT IN (SELECT id FROM addresses)');

        // 2. Use a Blueprint callback to safely modify the table.
        // With doctrine/dbal installed, Laravel can now correctly manage foreign keys.
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the old foreign key constraint if it exists.
            // Laravel will find it by the column name.
            $table->dropForeign(['address_id']);

            // Add the new, correct foreign key constraint that points to the unified `addresses` table.
            $table->foreign('address_id')
                  ->references('id')
                  ->on('addresses')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // To roll back, we just drop the new constraint.
            // A full rollback to the old state is complex and unnecessary.
            $table->dropForeign(['address_id']);
        });
    }
};
