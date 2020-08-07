@extends('layouts.app')

@section('view_title','Users (' . $output->users->total() .')')

@section('content')
  <div class="centum ph10">
    <div class="centum ph10">
      <h1>@yield('view_title')</h1>
    </div>

    {{-- Begin messages --}}
    @include('partials.messages')
    {{-- End messages --}}

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
          <div class="cell x40 p0">
            <div class="centum">
              <div class="cell x10 align-right-d">
                <span class="center-v"><label>From</label></span>
              </div>
              <div class="cell x40">
                <input type="date" name="from" value="{{ $output->from }}" />
              </div>
              <div class="cell x10 align-right-d">
                <span class="center-v"><label>To</label></span>
              </div>
              <div class="cell x40">
                <input type="date" name="to" value="{{ $output->to }}" />
              </div>
            </div>
          </div>
          <div class="cell x20 btn-wrap">
            <input type="submit" class="btn" value="Search" />
            <a class="btn" href="{{ route('users.index') }}">Clear</a>
          </div>
        </div>
      </form>
    </div>
    <div class="cell spacer-d"></div>
    {{-- End search filters --}}

    {{-- Begin desktop header --}}
    @if(0 < $output->users->total())
      <div class="centum">
        <div class="cell x10 header-d">
          ID <span class="sorters" data-key="id"></span>
        </div>
        <div class="cell x20 header-d">
          Username <span class="sorters" data-key="name"></span>
        </div>
        <div class="cell x20 header-d">
          Email <span class="sorters" data-key="email"></span>
        </div>
        <div class="cell x10 header-d">
          Role <span class="sorters" data-key="role"></span>
        </div>
        <div class="cell x20 header-d">
          Last login <span class="sorters" data-key="last_login"></span>
        </div>
        <div class="cell x20 header-d">
          Action
        </div>
      @endif
      {{-- End desktop header --}}

      {{-- Begin rows --}}
      @foreach($output->users as $user)
        <div class="centum striped-even">
          <div class="cell x10">
            <span class="header-m">ID:</span>
            <span class="center-v">{{ $user->id }}</span>
          </div>
          <div class="cell x20">
            <span class="header-m">Username:</span>
            <span class="center-v">{{ $user->name }}</span>
            @if($user->isSuspended())
              <i class="fas fa-ban" title="Suspended till {{ $user->suspended_till }} {{ config('app.timezone') }}"></i>
            @else
              @if($user->email_verified_at)
                <i class="fas fa-check" title="Verified"></i>
              @endif
            @endif
          </div>
          <div class="cell x20">
            <span class="header-m">Email:</span>
            <span class="center-v"><a href="mailto:{{ $user->email }}" target="_blank">{{ $user->email }}</a></span>
          </div>
          <div class="cell x10">
            <span class="header-m">Role:</span>
            <span class="center-v">{{ $user->getRole() }}</span>
          </div>
          <div class="cell x20">
            <span class="header-m">Last login:</span>
            <span class="center-v">{{ $user->last_login }}</span>
          </div>
          <div class="cell x20 btn-wrap">
            <a class="btn" href="{{ route('users.edit', $user->id) }}">Edit</a>
            <form class="inline" action="{{ route('users.suspend', $user->id) }}" method="get" id="suspendUser{{ $user->id }}">
              @csrf
              <input type="hidden" name="suspendedDays" value="7" />
              <input type="submit" class="btn secondary modal+suspendUser({{ $user->id }},{{ rawurlencode($user->name) }},suspendUser{{ $user->id }})" value="Suspend" />
            </form>
            <form class="inline" action="{{ route('users.destroy', $user->id) }}" method="post" id="deleteUser{{ $user->id }}">
              @method('DELETE')
              @csrf
              <input type="submit" class="btn danger modal+deleteUser({{ $user->id }},{{ rawurlencode($user->name) }},deleteUser{{ $user->id }})" value="Delete" />
            </form>
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
