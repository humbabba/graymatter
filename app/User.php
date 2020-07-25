<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /***
     * @param $role
     * @return boolean
     */
    public function hasRole($role)
    {
        $userRole = $this->getRole();
        $allowed = Roles::getAllowedRoles($role);
        if(in_array($role,$allowed)) {
          return true;
        }
        return false;
    }

    /**
     * @param array $role
     * @return $this
     */
    public function setRole(array $role)
    {
        $this->setAttribute('role', $role);
        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->getAttribute('role');
    }
}
