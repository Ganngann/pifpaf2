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
        Schema::table('items', function (Blueprint $table) {
            // Supprimer l'ancienne contrainte de clé étrangère
            $table->dropForeign(['pickup_address_id']);

            // Ajouter la nouvelle contrainte de clé étrangère
            $table->foreign('pickup_address_id')
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
        Schema::table('items', function (Blueprint $table) {
            // Supprimer la nouvelle contrainte
            $table->dropForeign(['pickup_address_id']);

            // Rétablir l'ancienne contrainte (si nécessaire, bien que cela ne soit pas idéal)
            // Note: On ne peut pas la rétablir car la table "pickup_addresses" n'existe plus.
            // On se contente de supprimer la nouvelle contrainte.
        });
    }
};
