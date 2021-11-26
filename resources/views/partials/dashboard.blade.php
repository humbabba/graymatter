@extends('layouts.broad')

@section('view_title','Dashboard')

@section('content.broad')
  <div class="centum ph10">
    <h1>@yield('view_title') - {{ Auth::user()->name }}</h1>
  </div>
  <div class="centum">
    <div class="cell x60">
      <p>Welcome.</p>
      <p><a href="{{ route('info') }}">Info</a></p>
    </div>
    <div class="cell x20 spacer-d">
    </div>
    <div class="cell x20 p0">
      <div class="centum">
        <div class="cell x100 shift+2">
            <p>Wonderful.</p>
            <p>Place.</p>
        </div>
        <div class="cell p5 spacer-m">
        </div>
        <div class="cell x100 p0 border-basic">
          <div class="cell">
              <p>Beautiful.</p>
              <p>Face.</p>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
