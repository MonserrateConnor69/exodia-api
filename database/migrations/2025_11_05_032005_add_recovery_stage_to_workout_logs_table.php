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
            if (!Schema::hasColumn('workout_logs', 'recovery_stage')) {
                $table->integer('recovery_stage')->default(1)->after('exercise_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_logs', function (Blueprint $table) {
            if (Schema::hasColumn('workout_logs', 'recovery_stage')) {
                $table->dropColumn('recovery_stage');
            }
        });
    }
};
