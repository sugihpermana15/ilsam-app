<?php

namespace App\Providers;

use App\Models\DailyTask;
use App\Policies\DailyTaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        DailyTask::class => DailyTaskPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
