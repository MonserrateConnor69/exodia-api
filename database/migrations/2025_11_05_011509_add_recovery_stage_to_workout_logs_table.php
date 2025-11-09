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
        // Add the new column. When a workout is logged, it defaults to stage 1 (Sore).
        $table->integer('recovery_stage')->unsigned()->default(1)->after('exercise_id');
    });
}

public function down(): void
{
    Schema::table('workout_logs', function (Blueprint $table) {
        $table->dropColumn('recovery_stage');
    });
}

};
