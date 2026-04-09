<?php

namespace App\Console\Commands;

use App\Traits\Manageable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync';
    protected $description = 'Sync CRUD permissions for all models using the Manageable trait';

    public function handle(): int
    {
        $totalCreated = 0;

        foreach ($this->getManageableModels() as $modelClass) {
            $created = $modelClass::syncPermissions();
            $prefix = $modelClass::permissionPrefix();

            if ($created > 0) {
                $this->info("Created {$created} permission(s) for {$prefix}");
                $totalCreated += $created;
            } else {
                $this->line("Permissions for {$prefix} already exist");
            }
        }

        if ($totalCreated === 0) {
            $this->info('All permissions are up to date.');
        } else {
            $this->info("Done. Created {$totalCreated} new permission(s).");
        }

        return self::SUCCESS;
    }

    public static function getManageableModels(): array
    {
        $models = [];
        $modelPath = app_path('Models');

        foreach (File::files($modelPath) as $file) {
            $className = 'App\\Models\\' . $file->getFilenameWithoutExtension();

            if (class_exists($className) && in_array(Manageable::class, class_uses_recursive($className))) {
                $models[] = $className;
            }
        }

        return $models;
    }
}
