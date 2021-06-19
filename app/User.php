<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Roles\UserRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'gender',
        'image',
        'bio',
        'last_login',
        'suspended_till',
        'suspended_message'
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
        $allowed = UserRoles::getAllowedRoles($this->getRole());
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

    /**
    * @return boolean
    */
    public function isSuspended()
    {
      $date = date('Y-m-d H:i:s');
      return $this->getAttribute('suspended_till') > $date;
    }

    public function nullSuspended()
    {
      $this->setAttribute('suspended_till',null);
      $this->save();
    }
}
