@extends('layouts.app')

@section('view_title','Edit user ' . $output->user->name .')

@section('content')
  <div class="centum ph10">
    <div class="centum ph10">
      <h1>@yield('view_title')</h1>
    </div>

    {{-- Begin messages --}}
    @include('partials.messages')
    {{-- End messages --}}

    {{-- Begin rows --}}

    {{-- End rows --}}
  </div>
@endsection
