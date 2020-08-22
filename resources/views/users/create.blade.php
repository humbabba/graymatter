@extends('layouts.app')

@section('view_title','Create user')

@section('content')
  <div class="centum ph10">
    <div class="cell x60 pv0">
      <h1>@yield('view_title')</h1>
    </div>

    {{-- Begin messages --}}
    @include('partials.messages')
    {{-- End messages --}}

    {{-- Begin form --}}
    <form class="centum input-spacing" action="{{ route('users.store') }}" method="post">
      <div class="cell x-max700 p0">
        <div class="centum">
          <div class="cell x50">
            <label>Username</label>
            <input type="text" name="name" value="{{ old('name') }}"/>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}"/>
            <label>Role</label>
            <select name="role" name="role">
              @foreach($output->roles as $index => $value)
                <option value="{{ $index }}"{{ (old('role','user') === $index)? ' selected':'' }}>{{ $value }}</option>
              @endforeach
            </select>
          </div>
          <div class="cell x50">
            <label>Password</label>
            <input type="password" />
            <label>Confirm password</label>
            <input type="password" />
          </div>
          <div class="cell pv0">
            <label>Bio</label>
            <input type="hidden" name="bio" class="text-editor" value="" />
          </div>
        </div>
      </div>
    </form>
    {{-- End form --}}
  </div>
@endsection