<?php

declare(strict_types=1);

namespace ToneGabes\Filament\Icons;

use Illuminate\Support\ServiceProvider;
use ToneGabes\Filament\Icons\Commands\SyncPhosphorIconsCommand;

/**
 * Service Provider for the Filament Phosphor Icons package
 */
class PhosphorIconsServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncPhosphorIconsCommand::class,
            ]);
        }
    }
}
