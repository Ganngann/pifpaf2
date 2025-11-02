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
            $table->boolean('delivery_available')->default(false)->after('pickup_available');
            $table->foreignId('pickup_address_id')->nullable()->constrained('pickup_addresses')->after('delivery_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['pickup_address_id']);
            $table->dropColumn(['delivery_available', 'pickup_address_id']);
        });
    }
};
