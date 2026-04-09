<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\User;
use App\Notifications\ModelCreatedNotification;
use Illuminate\Support\Facades\Schema;

trait Manageable
{
    public static function bootManageable(): void
    {
        static::created(function ($model) {
            $prefix = $model::permissionPrefix();

            // Don't notify for user creation — handled separately by the auth system
            if ($prefix === 'users') return;

            $admins = User::where('notify_on_create', '!=', null)
                ->whereHas('roles', fn($q) => $q->where('name', 'admin'))
                ->get()
                ->filter(fn($admin) => $admin->wantsCreateNotification($prefix));

            $label = $model::permissionLabel();
            $url = "/{$prefix}/{$model->id}";

            foreach ($admins as $admin) {
                $admin->notify(new ModelCreatedNotification($model, $label, $url));
            }
        });
    }

    public static function permissionPrefix(): string
    {
        return (new static)->getTable();
    }

    public static function permissionLabel(): string
    {
        return ucfirst(str_replace('_', ' ', static::permissionPrefix()));
    }

    public static function crudPermissions(): array
    {
        $prefix = static::permissionPrefix();
        $label = static::permissionLabel();

        return [
            "{$prefix}.view" => "View {$label}",
            "{$prefix}.create" => "Create {$label}",
            "{$prefix}.edit" => "Edit {$label}",
            "{$prefix}.delete" => "Delete {$label}",
        ];
    }

    public static function syncPermissions(): int
    {
        $table = (new static)->getTable();

        if (!Schema::hasTable($table)) {
            Permission::where('name', 'like', static::permissionPrefix() . '.%')->delete();
            return 0;
        }

        $created = 0;
        $existing = Permission::pluck('name')->flip();

        foreach (static::crudPermissions() as $name => $description) {
            if (!$existing->has($name)) {
                Permission::create(['name' => $name, 'description' => $description]);
                $created++;
            }
        }

        return $created;
    }

    public static function isPubliclyViewable(): bool
    {
        $prefix = '/' . static::permissionPrefix();

        return \App\Models\NavItem::tree()->contains(function ($item) use ($prefix) {
            if ($item->url === $prefix && $item->hasGuestAccess()) {
                return true;
            }

            return $item->children->contains(fn($c) => $c->url === $prefix && $c->hasGuestAccess());
        });
    }

    public function canView(?User $user): bool
    {
        if (static::isPubliclyViewable()) {
            return true;
        }

        if (!$user) {
            return false;
        }

        return $user->isAppAdmin() || $user->hasPermission(static::permissionPrefix() . '.view');
    }

    public function canManage(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->isAppAdmin() || $user->hasPermission(static::permissionPrefix() . '.edit');
    }
}
