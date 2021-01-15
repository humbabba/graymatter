@extends('layouts.app')

@section('view_title','User profile for ' . $user->name)

@section('content')
    <div class="centum ph10">
        <div class="cell x60 pv0">
            <h1>@yield('view_title')</h1>
        </div>

        {{-- Begin messages --}}
        @include('partials.messages')
        {{-- End messages --}}

        {{-- Begin content --}}
        <div class="cell x-max700 p0">
            <div class="centum">
                <div class="cell x50">
                    <label>Username</label>
                    <p>{{ $user->name }}</p>
                    <label>Email</label>
                    <p>{{ $user->email }}</p>
                    <label>Role</label>
                    <p>{{ $user->role }}</p>
                </div>
            </div>
        </div>
        {{-- End content --}}
    </div>
@endsection
