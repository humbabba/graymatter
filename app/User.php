<?php

namespace App;

use App\Http\Roles\UserRoles;
use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyEmail;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

  /**
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

  /**
  * @return boolean
  */
  public function isSelf()
  {
    return $this->getAttribute('id') === Auth::user()->id;
  }

  public function nullSuspended()
  {
    $this->setAttribute('suspended_till',null);
    $this->save();
  }

  /**
  * Send the password reset notification.
  *
  * @param  string  $token
  * @return void
  */
  public function sendPasswordResetNotification($token)
  {
    $this->notify(new CustomResetPassword($token));
  }

  /**
  * Send the email-verification notification.
  *
  * @return void
  */
  public function sendEmailVerificationNotification()
  {
    $this->notify(new CustomVerifyEmail);
  }

  /**
  * @param Illuminate\Http\Request $request
  * @return stdClass $output
  */
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

  /**
  * Writes a logger for a new user being created.
  */
  public function writeNewUserLogger()
  {
    $name = $this->name;
    $email = $this->email;
    $id = $this->id;
    $profileLink = route('users.profile',$id);
    $role = $this->getRole();

    $this->logAction('Create',"New user: '<a href='$profileLink'>$name</a>' ($role, <a href='mailto:$email' target='_blank'>$email</a>).");
  }

  /**
  * @param UserUpdateRequest $request
  * @param $id
  */
  public function updateUserInfo(UserUpdateRequest $request, $id)
  {
    $data = $request->validated();

    $loggerNotes = '';

    foreach($data as $index => $value) {
      if($this->$index !== $value) {
        $capKey = ucfirst($index);
        $loggerNotes .= "<p><b>$capKey</b> changed from '{$this->$index}' to '$value'.</p>";
      }
    }

    if(!empty($loggerNotes)) {
      $profileLink = route('users.profile',$this->id);
      $loggerNotes = "<p>User '<a href='$profileLink'>{$this->name}</a>' updated:</p>" . $loggerNotes;
      $this->logAction('Update',$loggerNotes);
    }

    $this->name = $data['name'];
    $this->email =  $data['email'];
    $this->role = $data['role'];

    $this->save();
  }

  public function suspend($days, $message)
  {
    $userName = $this->name;
    $this->suspended_till = date('Y-m-d H:i:s',strtotime('+' . $days . ' days'));
    $this->suspended_message = $message;
    $this->save();
  }

  /**
  * Destroy user record
  */
  public function delete()
  {
    $this->logAction('Delete',"<p>User '{$this->name}' deleted.</p>");

    parent::delete();
  }

  private function logAction($action,$notes)
  {
    $modelLink = '';
    if('Delete' !== $action) {
      $modelLink = route('users.profile',$this->id);
    }
    $actingUser = Auth::user();

    $logger = new Logger(
      [
        'username' => $actingUser->name,
        'user_id' => $actingUser->id,
        'model' => 'User',
        'model_id' => $this->id,
        'model_link' => $modelLink,
        'action' => $action,
        'notes' => $notes
      ]
    );
    $logger->save();
  }

}
