<?php

namespace App\Providers;

use App\Models\DailyTask;
use App\Models\Note;
use App\Policies\DailyTaskPolicy;
use App\Policies\NotePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        DailyTask::class => DailyTaskPolicy::class,
        Note::class => NotePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
