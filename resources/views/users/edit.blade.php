@extends('layouts.app')

@section('view_title','Edit user ' . $user->name)

@section('content')
    <div class="centum ph10">
        <div class="cell x60 pv0">
            <h1>@yield('view_title')</h1>
        </div>

        {{-- Begin form --}}
        <form class="centum input-spacing" action="{{ route('users.update',$user->id) }}" method="post">
            @method('PATCH')
            @csrf
            <div class="cell x-max700 p0">
                <div class="centum">
                    <div class="cell x50">
                        <label>Username</label>
                        <input type="text" name="name" value="{{ old('name',$user->name) }}"/>
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email',$user->email) }}"/>
                        <label>Role</label>
                        <select name="role" name="role">
                            @foreach($roles as $index => $value)
                                <option value="{{ $index }}"{{ ($user->role === $index)? ' selected':'' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cell x50 align-center" id="passwordInputDiv">
                        <span class="center-v">
                            <button class="btn secondary">
                                Reset password
                            </button>
                        </span>
                        <div id="passwordTemplateDiv" style="display:none">
                            <label>Password</label>
                            <input type="password" name="password" />
                            <label>Confirm password</label>
                            <input type="password" name="confirmPassowrd" />
                            @include('partials.password_rules')
                        </div>
                    </div>
                    <div class="cell pv0">
                        <label>Bio</label>
                        <input type="hidden" name="bio" class="text-editor" value="{{ $user->bio }}" />
                    </div>
                </div>
            </div>
            <div class="cell">
                <input type="submit" class="btn" value="Update" />
            </div>
        </form>
        {{-- End form --}}
    </div>
@endsection
