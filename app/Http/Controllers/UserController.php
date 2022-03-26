<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Roles\UserRoles;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
  /**
  * Display a listing of the resource.
  * @param Request $request
  * @param array $msg
  * @return Factory|View
  */
  public function index(Request $request)
  {
    $output = User::getSearchedUsers($request);

    return view('users.index')->with('output', $output)->with('error', $output->error);
  }

  /**
  * Show the form for creating a new resource.
  *
  * @return Factory|View
  */
  public function create()
  {
    //Check if user creation is allowed
    abort_if(!config('users.new.create'),404);

    return view('users.create')->with('roles',UserRoles::getRoleList());
  }

  /**
  * Store a newly created resource in storage.
  *
  * @param Request $request
  * @return void
  */
  public function store(UserStoreRequest $request)
  {
    $data = $request->validated();
    $data['password'] = Hash::make($data['password']);
    $user = new User($data);
    $user->save();
    $user->writeNewUserLogger();
    event(new Registered($user));

    return redirect(route('users.edit', $user->id))->with('success', "User '{$user->name}' created. A verification email was sent to '{$user->email}.'");
  }

  /**
  * Display the specified resource.
  *
  * @param int $id
  * @return Factory|RedirectResponse|Redirector|View
  */
  public function show($id)
  {
    $user = User::find($id);

    if(is_null($user)) { //User not found
      return redirect(route('users.index'))->with('error','No user found with ID ' . $id);
    }
    return view('users.show')->with('user',$user);
  }

  /**
  * Show the form for editing the specified resource.
  *
  * @param int $id
  * @return Factory|\Illuminate\Contracts\View\View|RedirectResponse|Redirector
  */
  public function edit($id)
  {
    $user = User::find($id);

    if (is_null($user)) { //User not found
      return redirect()->route('users.index')->with('error', 'No user found with ID ' . $id);
    }

    return view('users.edit')->with('user',$user)->with('roles',UserRoles::getRoleList());
  }

  /**
  * Update the specified resource in storage.
  *
  * @param Request $request
  * @param int $id
  * @return Factory|\Illuminate\Contracts\View\View|RedirectResponse
  */
  public function update(UserUpdateRequest $request, $id)
  {
    $user = User::find($id);

    if (is_null($user)) { //User not found
      return redirect()->route('users.index')->with('error', 'No user found with ID ' . $id);
    }

    $user->updateUserInfo($request, $id);

    return redirect()->route('users.edit',$user->id)->with('success', 'User update successful.');
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param int $id
  * @return RedirectResponse|Redirector
  */
  public function destroy($id)
  {
    $user = User::find($id);

    if (is_null($user)) { //User not found
      return redirect()->route('users.index')->with('error', 'No user found with ID ' . $id);
    }

    if($user->isSelf()) { //Trying to suspend self
      return redirect()->route('users.index')->with('error', 'Self-deletion is prohibited by the laws of robotics.');
    }

    $user->delete();

    return redirect()->route('users.index')->with('success', "Successfully deleted user '{$user->name}' (ID: $id).");
  }

  /**
  * Update the specified resource in storage.
  *
  * @param Request $request
  * @param  int  $id
  * @param  int  $days
  * @return Response
  */
  public function suspend(Request $request, $id)
  {
    $user = User::find($id);

    if (is_null($user)) { //User not found
      return redirect()->route('users.index')->with('error', 'No user found with ID ' . $id);
    }

    if($user->isSelf()) { //Trying to suspend self
      return redirect()->route('users.index')->with('error', 'Self-suspension is prohibited by the laws of robotics.');
    }

    $userName = $user->name;

    $days = $request->get('suspendedDays');
    if(!is_numeric($days)) {
      return redirect(route('users.index'))->with('error','No suspension duration input found.');
    }
    $message = $request->get('suspendedMessage');

    $user->suspend($days,$message);

    if('0' === $days) {
      return redirect()->route('users.index')->with('success',"User '$userName' restored.");
    }
    $daysText = 'days';
    if('1' === $days) {
      $daysText = 'day';
    }
    return redirect()->route('users.index')->with('success',"User '$userName' suspended for $days $daysText.");
  }
}
