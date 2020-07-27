@extends('layouts.app')

@section('view_title','Users (' . $output->users->total() .')')

@section('content')
  <div class="centum ph10">
    <div class="centum ph10">
      <h1>@yield('view_title')</h1>
    </div>

    {{-- Begin search filters --}}
    <div class="cell p0">
      <form action="" method="get">
        <div class="centum border-basic">
          <div class="cell x20">
            <input type="search" name="search" placeholder="Name, email, ID" value="{{ $output->search }}" />
          </div>
            <div class="cell x20">
              <select name="role">
                  <option value="">Filter by role</option>
                @foreach($output->roles as $index => $value)
                  <option value="{{ $index }}"{{ ($output->role === $index)? ' selected':'' }}>{{ $value }}</option>
                @endforeach
                  <option value="">Clear filter</option>
              </select>
            </div>
          <div class="cell x60">
            <input type="submit" class="btn" value="Search" />
          </div>
        </div>
      </form>
    </div>
    <div class="cell spacer-d"></div>
    {{-- End search filters --}}

    {{-- Begin desktop header --}}
    <div class="centum">
      <div class="cell x10 header-d">
        ID
      </div>
      <div class="cell x25 header-d">
        Username
      </div>
      <div class="cell x25 header-d">
        Email
      </div>
      <div class="cell x15 header-d">
        Role
      </div>
      <div class="cell x25 header-d">
        Action
      </div>
      {{-- End desktop header --}}

      {{-- Begin rows --}}
      @foreach($output->users as $user)
        <div class="centum striped-even">
          <div class="cell x10">
            <span class="header-m">ID:</span>
            <span class="center-v">{{ $user->id }}</span>
          </div>
          <div class="cell x25">
            <span class="header-m">Username:</span>
            <span class="center-v">{{ $user->name }}</span>
            @if($user->email_verified_at)
              <i class="fas fa-check" title="Verified"></i>
            @endif
          </div>
          <div class="cell x25">
            <span class="header-m">Email:</span>
            <span class="center-v"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></span>
          </div>
          <div class="cell x15">
            <span class="header-m">Role:</span>
            <span class="center-v">{{ $user->getRole() }}</span>
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
      {{ $output->users->appends(request()->query())->links() }}
    </div>
  </div>
@endsection
