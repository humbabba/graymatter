@extends('layouts.narrow')

@section('view_title','User profile for ' . $user->name)

@section('content.narrow')
<div class="centum">
  <div class="cell">
    <h1>@yield('view_title')</h1>
  </div>

  {{-- Begin content --}}
  <div class="cell x-max700">
    <div class="centum">
      <div class="cell">
        <img src="{{ getGravatarSrc(Auth::user()->email,200) }}"/>
        @if($user->isSelf())
            <p class="small"><i><a href="https://en.gravatar.com/emails/" target="_blank">Set Gravatar image</a> (changes may take a while to appear)</i></p>
        @endif
      </div>
      <div class="cell">
        <label>Role</label>
        {{ ucfirst($user->role) }}
      </div>
      @if(!empty($user->bio))
        <div class="cell">
          <label>Bio</label>
          {!! $user->bio !!}
        </div>
      @endif
    </div>
  </div>
  {{-- End content --}}
</div>
@endsection
