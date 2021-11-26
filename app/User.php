<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Http\Roles\UserRoles;
use Illuminate\Http\Request;

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

    public static function getSearchedUsers(Request $request)
    {
        //Deal with filter params
        $search = $request->get('search');
        $role = $request->get('role');
        $from = $request->get('from');
        $to = $request->get('to');
        $orderBy = $request->get('orderBy') ?? 'id';
        $direction = $request->get('direction') ?? 'asc';
        $csv = $request->get('csv');

        $output = new \stdClass();

        //Users
        $output->users = User::where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('id', '=', $search);
        })
            ->where(function ($query) use ($role) {
                if ($role) {
                    $query->where('role', '=', $role);
                }
            })
            ->where(function ($query) use ($from) {
                if ($from) {
                    $query->where('last_login', '>=', $from . ' 00:00:00');
                }
            })
            ->where(function ($query) use ($to) {
                if ($to) {
                    $query->where('last_login', '<=', $to . ' 23:59:59');
                }
            })
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        //Other output values
        $output->roles = UserRoles::getRoleList();
        $output->search = $search;
        $output->role = $role;
        $output->from = $from;
        $output->to = $to;
        $output->total = $output->users->total();

        $output->error = '';
        if (0 === $output->total) {
            $output->error = 'No users found. Try <a href="' . route('users.index') . '">clearing the filters</a>.';
        }

        if($csv) {
            $content = "ID,Username,Email,Role,Last Login\r\n";

            foreach($output->users as $user) {
                $content .= quotesWrap($user->id) . ',';
                $content .= quotesWrap($user->name) . ',';
                $content .= quotesWrap($user->email) . ',';
                $content .= quotesWrap(ucfirst($user->getRole())) . ',';
                $content .= quotesWrap($user->last_login) . "\r\n";
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-disposition: attachment; filename=users.csv');
            header('Content-Length: '.strlen($content));
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            header('Pragma: public');
            echo $content;
        }

        return $output;
    }
}
