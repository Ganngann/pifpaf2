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
        Schema::table('transactions', function (Blueprint $table) {
            // Supprimer l'ancienne contrainte de clé étrangère
            $table->dropForeign(['shipping_address_id']);

            // Ajouter la nouvelle contrainte de clé étrangère
            $table->foreign('shipping_address_id')
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
            // Supprimer la nouvelle contrainte
            $table->dropForeign(['shipping_address_id']);
        });
    }
};
