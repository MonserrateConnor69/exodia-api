<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\WorkoutLog;       // ✅ ADD THIS LINE
use App\Policies\WorkoutLogPolicy;  // ✅ ADD THIS LINE

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        WorkoutLog::class => WorkoutLogPolicy::class,
    ];

    
    public function boot(): void
    {
        //
    }
}