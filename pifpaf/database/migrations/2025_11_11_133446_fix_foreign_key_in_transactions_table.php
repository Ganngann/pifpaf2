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
        // 1. Clean up orphaned data.
        DB::statement('UPDATE transactions SET address_id = NULL WHERE address_id IS NOT NULL AND address_id NOT IN (SELECT id FROM addresses)');

        // 2. Drop the old foreign key if it exists.
        Schema::table('transactions', function (Blueprint $table) {
            // Check for the original foreign key name.
            if ($this->hasForeignKey('transactions', 'transactions_shipping_address_id_foreign')) {
                $table->dropForeign('transactions_shipping_address_id_foreign');
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

    /**
     * Helper to check if a foreign key exists.
     */
    private function hasForeignKey(string $table, string $name): bool
    {
        $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys($table);

        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getName() === $name) {
                return true;
            }
        }

        return false;
    }
};
