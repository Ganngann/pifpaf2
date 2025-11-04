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
            $table->foreignId('shipping_address_id')->nullable()->after('pickup_code')->constrained('shipping_addresses');
            $table->string('sendcloud_parcel_id')->nullable()->after('shipping_address_id');
            $table->string('tracking_code')->nullable()->after('sendcloud_parcel_id');
            $table->text('label_url')->nullable()->after('tracking_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['shipping_address_id']);
            $table->dropColumn(['shipping_address_id', 'sendcloud_parcel_id', 'tracking_code', 'label_url']);
        });
    }
};
