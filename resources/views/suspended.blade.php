@extends('layouts.app')

@section('content')
  <div class="centum">
      <div class="cell align-center">
          <h1>User suspended</h1>
          <p>User{{ (!empty($user->name))? ' ' . $user->name . ' ':' ' }}is suspended{{ (!empty($user->suspended_till))? ' till ' . $user->suspended_till . ' ' . config('app.timezone'):'' }}.</p>
      </div>
      @if(!empty($user->suspended_message))
          <div class="cell align-center">
            <p>Message from admin:</p>
          </div>
        <div class="cell border-basic x50 center-h">
          {!! $user->suspended_message !!}
        </div>
      @endif
  </div>
@endsection
