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
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the old foreign key constraint that points to shipping_addresses
            // We need to check if the constraint exists before dropping it to avoid errors on fresh installs
            if (DB::getSchemaBuilder()->hasForeignKey('transactions', ['address_id'])) {
                $table->dropForeign('transactions_shipping_address_id_foreign');
            }
        });

        // Clean up orphaned address_id records before creating the new constraint
        DB::statement('UPDATE transactions SET address_id = NULL WHERE address_id IS NOT NULL AND address_id NOT IN (SELECT id FROM addresses)');

        Schema::table('transactions', function (Blueprint $table) {
            // Add the new foreign key constraint that points to the unified addresses table
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['address_id']);

            // Re-add the old foreign key constraint for rollback safety
            // Note: This assumes the old 'shipping_addresses' table still exists.
            // If it was deleted, this rollback would fail.
            $table->foreign('address_id', 'transactions_shipping_address_id_foreign')->references('id')->on('shipping_addresses')->onDelete('set null');
        });
    }
};
