<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuspendedController extends Controller
{
    public function index(Request $request)
    {
      $user = Auth::user();
      if($user->isSuspended()) {
        return view('suspended')->with(compact('user'));
      }
      return redirect(route('dashboard'));
    }
}
