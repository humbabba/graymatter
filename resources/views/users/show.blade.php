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
                    <label>Username</label>
                    <p>{{ $user->name }}</p>
                    <label>Role</label>
                    <p>{{ ucfirst($user->role) }}</p>
                </div>
            </div>
        </div>
        {{-- End content --}}
    </div>
@endsection
