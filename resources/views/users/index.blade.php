@extends('layouts.app')

@section('view_title','Users')

@section('content')
  <div class="centum ph10">
    <div class="centum ph10">
      <h1>@yield('view_title')</h1>
    </div>
    {{-- Begin desktop header --}}
    <div class="centum">
      <div class="cell x25 header-d">
        Username
      </div>
      <div class="cell x25 header-d">
        Email
      </div>
      <div class="cell x25 header-d">
        Role
      </div>
      <div class="cell x25 header-d">
        Action
      </div>
      {{-- End desktop header --}}

      {{-- Begin rows --}}
      @foreach($output->users as $user)
        <div class="centum striped-even">
          <div class="cell x25">
            <span class="header-m">Username:</span>
            {{ $user->name }}
            @if($user->email_verified_at)
              <i class="fas fa-check" title="Verified"></i>
            @endif
          </div>
          <div class="cell x25">
            <span class="header-m">Email:</span>
            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
          </div>
          <div class="cell x25">
            <span class="header-m">Role:</span>
            {{ $user->getRole() }}
          </div>
          <div class="cell x25">
            <input type="submit" class="btn" value="Edit"/>
            <input type="submit" class="btn" value="Delete"/>
          </div>
        </div>
      @endforeach
      {{-- End rows --}}
    </div>
    <div class="centum pv15">
      {{ $output->users->links() }}
    </div>
  </div>
@endsection
