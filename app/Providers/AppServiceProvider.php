<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            $command = (string) ($event->command ?? '');

            // Guard against accidentally wiping a real database.
            $destructive = [
                'migrate:fresh',
                'migrate:refresh',
                'db:wipe',
            ];

            if (! in_array($command, $destructive, true)) {
                return;
            }

            $allowed = (string) env('ALLOW_DESTRUCTIVE_COMMANDS', '');
            if ($allowed === '1' || strtolower($allowed) === 'true' || strtolower($allowed) === 'yes') {
                return;
            }

            fwrite(STDERR, "\n[SAFETY] Blocked '{$command}' because it destroys data.\n");
            fwrite(STDERR, "Set ALLOW_DESTRUCTIVE_COMMANDS=1 in your .env to run it intentionally.\n\n");
            exit(1);
        });
    }
}
