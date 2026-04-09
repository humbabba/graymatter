<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class NavItem extends Model
{
    protected $fillable = [
        'label',
        'url',
        'parent_id',
        'sort_order',
        'roles',
    ];

    protected $casts = [
        'roles' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function isVisibleTo(?User $user): bool
    {
        if (!$user) {
            return $this->hasGuestAccess()
                || ($this->relationLoaded('children') && $this->children->contains(fn($c) => $c->hasGuestAccess()));
        }

        if (empty($this->roles)) {
            return true;
        }

        // Filter out 'guest' — it's a virtual role, not a real DB role
        $realRoles = array_filter($this->roles, fn($r) => $r !== 'guest');

        if (empty($realRoles)) {
            return true;
        }

        return $user->roles()->whereIn('name', $realRoles)->exists();
    }

    public function hasGuestAccess(): bool
    {
        return is_array($this->roles) && in_array('guest', $this->roles);
    }

    public static function tree(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('nav_items_tree', 3600, function () {
            return static::whereNull('parent_id')
                ->with(['children' => fn($q) => $q->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('nav_items_tree');
    }
}
