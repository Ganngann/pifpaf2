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
        Schema::table('addresses', function (Blueprint $table) {
            $table->boolean('is_for_pickup')->default(false)->after('type');
            $table->boolean('is_for_delivery')->default(false)->after('is_for_pickup');
        });

        // Migrate existing data
        DB::table('addresses')->where('type', 'pickup')->update(['is_for_pickup' => true]);
        DB::table('addresses')->where('type', 'delivery')->update(['is_for_delivery' => true]);

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('type')->nullable()->after('user_id');
        });

        // Restore data based on the boolean flags
        // We prioritize pickup if an address is both
        DB::table('addresses')->where('is_for_pickup', true)->update(['type' => 'pickup']);
        DB::table('addresses')->where('is_for_delivery', true)->where('is_for_pickup', false)->update(['type' => 'delivery']);

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('is_for_pickup');
            $table->dropColumn('is_for_delivery');
        });
    }
};
