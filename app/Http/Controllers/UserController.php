<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Roles\UserRoles;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Deal with filter params
        $search = $request->get('search');
        $role = $request->get('role');
        $from = $request->get('from');
        $to = $request->get('to');

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
          ->paginate(2);

        //Other output values
        $output->roles = UserRoles::getRoleList();
        $output->search = $search;
        $output->role = $role;
        $output->from = $from;
        $output->to = $to;
        $output->msg = [];


        if(0 === $output->users->total()) {
          $output->msg[] = ['notice' => 'No users found. Try <a href="' . route('users.index') . '">clearing the filters</a>.'];
        }
        return view('users.index',compact('output'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  int  $days
     * @return \Illuminate\Http\Response
     */
    public function suspend(Request $request, $id)
    {
      $user = User::find($id);
      if($user) {
        $days = $request->get('suspendedDays');
        $userName = $user->name;
        $user->suspended_till = date('Y-m-d H:i:s',strtotime('+' . $days . ' days'));
        $user->save();
        return redirect(route('users.index'))->with('success','User "' . $userName . '" suspended for ' . $days . ' days.');
      }
    }
}
