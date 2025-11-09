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
        Schema::table('users', function (Blueprint $table) {
            // Change columns to allow for realistic values
            // DECIMAL(5, 1) allows numbers like 999.9 or 1000.0
            $table->decimal('weight', 5, 1)->change(); 
            $table->decimal('height', 5, 1)->change();
            $table->unsignedSmallInteger('age')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // This allows you to undo the change if needed
            $table->integer('weight')->change();
            $table->integer('height')->change();
            $table->integer('age')->change();
        });
    }
};