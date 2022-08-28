@extends('layouts.narrow')

@section('view_title','Create user')

@section('content.narrow')
    <div class="centum">
        <div class="cell">
            <h1>@yield('view_title')</h1>
        </div>

        {{-- Begin form --}}
        <form class="inputspace" action="{{ route('users.store') }}" method="post">
            @csrf
            <div class="cell x-max700 p0">
                <div class="centum">
                    <div class="cell x50">
                        <label>Username</label>
                        <input type="text" name="name" value="{{ old('name') }}"/>
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"/>
                        <label>Role</label>
                        <select name="role" name="role">
                            @foreach($roles as $index => $value)
                                <option value="{{ $index }}"{{ (old('role','user') === $index)? ' selected':'' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cell x50">
                        <label>Password</label>
                        <input type="password" name="password" required/>
                        <label>Confirm password</label>
                        <input type="password" name="password_confirmation" required />
                        @include('partials.password_rules')
                    </div>
                    <div class="cell pt0">
                        <label>Bio</label>
                        <input type="hidden" name="bio" class="textEditor" value="" />
                    </div>
                </div>
            </div>
            <div class="cell">
                <input type="submit" class="btn" value="Create" />
            </div>
        </form>
        {{-- End form --}}
    </div>
@endsection
