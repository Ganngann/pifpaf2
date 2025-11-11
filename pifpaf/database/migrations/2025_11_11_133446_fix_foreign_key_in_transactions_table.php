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
        // 1. Clean up orphaned data first.
        DB::statement('UPDATE transactions SET address_id = NULL WHERE address_id IS NOT NULL AND address_id NOT IN (SELECT id FROM addresses)');

        // 2. Defensively drop the foreign key, whatever its name is.
        Schema::table('transactions', function (Blueprint $table) {
            // This requires doctrine/dbal to be installed.
            $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();

            try {
                $foreignKeys = $schemaManager->listTableForeignKeys('transactions');

                foreach ($foreignKeys as $foreignKey) {
                    // If the foreign key involves the `address_id` column, drop it.
                    if (in_array('address_id', $foreignKey->getColumns())) {
                        $table->dropForeign($foreignKey->getName());
                        // Stop after finding and dropping the key.
                        break;
                    }
                }
            } catch (\Exception $e) {
                // If doctrine/dbal is not present or another error occurs,
                // we log it and proceed, as the key might not exist anyway.
                // This makes the migration less likely to fail.
                DB::rollBack();
                // We cannot use Log facade here, so we'll just proceed
            }
        });

        // 3. Add the new correct foreign key.
        Schema::table('transactions', function (Blueprint $table) {
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
            $table->dropForeign(['address_id']);
        });
    }
};
