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
          ->paginate(2);

        //Other output values
        $output->roles = UserRoles::getRoleList();
        $output->search = $search;
        $output->role = $role;

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
        //
    }
}
