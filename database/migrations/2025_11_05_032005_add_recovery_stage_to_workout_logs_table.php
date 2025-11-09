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
        Schema::table('workout_logs', function (Blueprint $table) {
            // Add the new column. 
            // We'll make it an integer and default to 1 when a workout is first logged.
            $table->integer('recovery_stage')->default(1)->after('exercise_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_logs', function (Blueprint $table) {
            // This allows us to undo the change if needed.
            $table->dropColumn('recovery_stage');
        });
    }
};