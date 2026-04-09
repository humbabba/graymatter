<?php

namespace App\Providers;

use App\Console\Commands\SyncPermissions;
use App\Traits\Manageable;
use Illuminate\Support\Facades\Schema;
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
        try {
            if (Schema::hasTable('permissions')) {
                foreach (SyncPermissions::getManageableModels() as $modelClass) {
                    $modelClass::syncPermissions();
                }
            }
        } catch (\Throwable) {
            // Database not available yet (e.g., during composer install before migration)
        }
    }
}
