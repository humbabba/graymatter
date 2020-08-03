@extends('layouts.app')

@php
  if(isset($_GET['user']) && !empty($_GET['user'])) {
    $userId = $_GET['user'];
    $user = \App\User::find($userId);
  }
@endphp

@section('content')
  <div class="centum">
      <div class="cell align-center">
          <h1>User suspended</h1>
          <p>User{{ (!empty($user->name))? ' ' . $user->name . ' ':' ' }}is suspended{{ (!empty($user->suspended_till))? ' till ' . $user->suspended_till . ' UTC':'' }}.</p>
      </div>
  </div>
@endsection
