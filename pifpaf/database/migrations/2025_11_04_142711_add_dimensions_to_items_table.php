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
            $table->integer('weight')->nullable()->after('price')->comment('Weight in grams');
            $table->integer('width')->nullable()->after('weight')->comment('Width in cm');
            $table->integer('height')->nullable()->after('width')->comment('Height in cm');
            $table->integer('length')->nullable()->after('height')->comment('Length in cm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['weight', 'width', 'height', 'length']);
        });
    }
};
