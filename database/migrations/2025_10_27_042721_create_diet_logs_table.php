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
    Schema::create('diet_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('food_name');
        $table->integer('calories');
        $table->decimal('protein', 5, 2)->nullable();
        $table->decimal('carbs', 5, 2)->nullable();
        $table->decimal('fat', 5, 2)->nullable();
        $table->timestamps(); // This automatically gives us the date
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_logs');
    }
};
