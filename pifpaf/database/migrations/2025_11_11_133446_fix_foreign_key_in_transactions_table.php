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
        // 1. Clean up orphaned address_id records before altering constraints.
        // This ensures data integrity before the new rule is applied.
        DB::statement('UPDATE transactions SET address_id = NULL WHERE address_id IS NOT NULL AND address_id NOT IN (SELECT id FROM addresses)');

        // 2. Use a Blueprint callback to safely modify the table.
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the old, incorrectly named foreign key constraint.
            // With doctrine/dbal, Laravel can automatically find the constraint by the column name.
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
            // To roll back, we drop the new constraint.
            $table->dropForeign(['address_id']);

            // And re-add the old one. This part is for safety, but assumes
            // the old `shipping_addresses` table still exists.
            $table->foreign('address_id', 'transactions_shipping_address_id_foreign')
                  ->references('id')
                  ->on('shipping_addresses')
                  ->onDelete('set null');
        });
    }
};
