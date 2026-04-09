<?php

namespace App\Models;

use App\Traits\HasRoles;
use App\Traits\Loggable;
use App\Traits\Manageable;
use App\Traits\Searchable;
use App\Traits\Trashable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Loggable, Manageable, Notifiable, Searchable, Trashable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'starting_view',
        'theme',
        'last_login_at',
        'news_viewed_at',
        'notify_on_new_user',
        'notify_on_create',
        'users_viewed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'auth_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'news_viewed_at' => 'datetime',
            'auth_code_expires_at' => 'datetime',
            'notify_on_new_user' => 'boolean',
            'notify_on_create' => 'array',
            'users_viewed_at' => 'datetime',
        ];
    }

    public function createdProjects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function isAppAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    protected function getRelationshipsForTrash(): array
    {
        return [
            'roles' => $this->roles()->pluck('roles.id')->toArray(),
        ];
    }

    public function getLoggableExcludedFields(): array
    {
        return ['updated_at', 'remember_token', 'password', 'auth_code', 'auth_code_expires_at', 'last_login_at', 'news_viewed_at', 'users_viewed_at'];
    }

    public function wantsCreateNotification(string $prefix): bool
    {
        return is_array($this->notify_on_create) && in_array($prefix, $this->notify_on_create);
    }

    public function hasPassword(): bool
    {
        return !is_null($this->password);
    }

    public function generateAuthCode(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->auth_code = Hash::make($code);
        $this->auth_code_expires_at = now()->addMinutes(10);
        $this->save();
        return $code;
    }

    public function verifyAuthCode(string $code): bool
    {
        if (!$this->auth_code || !$this->auth_code_expires_at) {
            return false;
        }
        if ($this->auth_code_expires_at->isPast()) {
            return false;
        }
        return Hash::check($code, $this->auth_code);
    }

    public function clearAuthCode(): void
    {
        $this->update(['auth_code' => null, 'auth_code_expires_at' => null]);
    }
}
