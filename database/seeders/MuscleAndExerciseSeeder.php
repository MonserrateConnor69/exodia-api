<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MuscleGroup;
use App\Models\Exercise;

class MuscleAndExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // --- UPPER BODY ---
    $chest = MuscleGroup::create(['name' => 'Chest']);
    $chest->exercises()->createMany([
        ['name' => 'Bench Press'],
        ['name' => 'Incline Dumbbell Press'],
        ['name' => 'Push-ups'],
    ]);

    $back = MuscleGroup::create(['name' => 'Back']);
    $back->exercises()->createMany([
        ['name' => 'Pull-ups'],
        ['name' => 'Bent Over Rows'],
        ['name' => 'Lat Pulldowns'],
    ]);


    $core = MuscleGroup::create(['name' => 'Core']);
    $core->exercises()->createMany([
        ['name' => 'Crunches'],
        ['name' => 'Plank'],
        ['name' => 'Leg Raises'],
    ]);

    $shoulders = MuscleGroup::create(['name' => 'Shoulders']);
    $shoulders->exercises()->createMany([
        ['name' => 'Overhead Press'],
        ['name' => 'Lateral Raises'],
    ]);

    $biceps = MuscleGroup::create(['name' => 'Biceps']);
    $biceps->exercises()->createMany([
        ['name' => 'Barbell Curls'],
        ['name' => 'Hammer Curls'],
    ]);

    $triceps = MuscleGroup::create(['name' => 'Triceps']);
    $triceps->exercises()->createMany([
        ['name' => 'Tricep Pushdowns'],
        ['name' => 'Skull Crushers'],
    ]);

    // --- LOWER BODY ---
    $quads = MuscleGroup::create(['name' => 'Quads']);
    $quads->exercises()->createMany([
        ['name' => 'Squats'],
        ['name' => 'Leg Press'],
        ['name' => 'Lunges'],
    ]);

    $hamstrings = MuscleGroup::create(['name' => 'Hamstrings']);
    $hamstrings->exercises()->createMany([
        ['name' => 'Deadlifts'],
        ['name' => 'Leg Curls'],
    ]);

    $calves = MuscleGroup::create(['name' => 'Calves']);
    $calves->exercises()->createMany([
        ['name' => 'Calf Raises'],
    ]);

        // You can add more muscle groups and exercises here!
    }
}