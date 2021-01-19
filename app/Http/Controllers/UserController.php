<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\User;
use App\Roles\UserRoles;
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
    public function index(Request $request, $msg = [])
    {
        //Deal with filter params
        $search = $request->get('search');
        $role = $request->get('role');
        $from = $request->get('from');
        $to = $request->get('to');
        $orderBy = $request->get('orderBy') ?? 'id';
        $direction = $request->get('direction') ?? 'asc';

        $output = new \stdClass();

        //Users
        $output->users = User::where(function($query) use($search) {
          $query->where('name','like','%' . $search . '%')
            ->orWhere('email','like','%' . $search . '%')
            ->orWhere('id','=',$search);
        })
          ->where(function($query) use($role) {
            if($role) {
              $query->where('role','=',$role);
            }
          })
          ->where(function($query) use($from) {
            if($from) {
              $query->where('last_login','>=',$from . ' 00:00:00');
            }
          })
          ->where(function($query) use($to) {
            if($to) {
              $query->where('last_login','<=',$to . ' 23:59:59');
            }
          })
          ->orderBy($orderBy,$direction)
          ->paginate(10);

        //Other output values
        $output->roles = UserRoles::getRoleList();
        $output->search = $search;
        $output->role = $role;
        $output->from = $from;
        $output->to = $to;
        $output->msg = $msg;


        if(0 === $output->users->total()) {
          $output->msg[] = ['notice' => 'No users found. Try <a href="' . route('users.index') . '">clearing the filters</a>.'];
        }

        return view('users.index',compact('output'));
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

        $roles = UserRoles::getRoleList();
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email'=>'required|email|unique:users,email,',
            'password' => 'required|confirmed|regex:/^(?=\P{Ll}*\p{Ll})(?=\P{Lu}*\p{Lu})(?=\P{N}*\p{N})(?=[\p{L}\p{N}]*[^\p{L}\p{N}])[\s\S]{8,}$/',
        ]);

        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => $request->get('role'),
            'bio' => $request->get('bio'),
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        $user->save();
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
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);

        if(is_null($user)) { //User not found
            return redirect(route('users.index'))->with('error','No user found with ID ' . $id);
        }
        $roles = UserRoles::getRoleList();
        return view('users.edit',compact('user','roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if($user) {
          $userName = $user->name;
          $user->delete();
          return redirect(route('users.index'))->with('success','Successfully deleted user "' . $userName . '" (ID: ' . $id .').');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @param  int  $days
     * @return \Illuminate\Http\Response
     */
    public function suspend(Request $request, $id)
    {
      $user = User::find($id);
      if($user) {
        $days = $request->get('suspendedDays');
        if(!is_numeric($days)) {
          return redirect(route('users.index'))->with('error','No suspension duration input found.');
        }
        $message = $request->get('suspendedMessage');
        $userName = $user->name;
        $user->suspended_till = date('Y-m-d H:i:s',strtotime('+' . $days . ' days'));
        $user->suspended_message = $message;
        $user->save();
        if('0' === $days) {
          return redirect(route('users.index'))->with('success','User "' . $userName . '" restored.');
        }
        $daysText = 'days';
        if('1' === $days) {
          $daysText = 'day';
        }
        return redirect(route('users.index'))->with('success','User "' . $userName . '" suspended for ' . $days . ' ' . $daysText . '.');
      }
      return redirect(route('users.index'))->with('error','No user found with ID ' . $id . '.');
    }
}
