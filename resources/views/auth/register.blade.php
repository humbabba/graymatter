@extends('layouts.app')

@section('content')
  <div class="centum">
     <div class="cell x30 center-h">
        <h1 class="pv15">{{ __('Register') }}</h1>
        @error('name')
          <div class="alert error" style="display: none">
            {{ $message }}
          </div>
        @enderror
        @error('email')
          <div class="alert error" style="display: none">
            {{ $message }}
          </div>
        @enderror
        @error('password')
          <div class="alert error" style="display: none">
            <strong>{{ $message }}</strong>
          </div>
        @enderror
        <form class="input-spacing" method="POST" action="{{ route('register') }}">
          @csrf
          <label for="name">{{ __('Username (public on site)') }}</label>
          <input id="name" type="text" name="name" value="{{ old('name') }}" class="@error('name') is-invalid @enderror" required autofocus>
          <label for="email">{{ __('Email address (never published on site)') }}</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
          <label for="password">{{ __('Password') }}</label>
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
          <label for="password-confirm">{{ __('Confirm password') }}</label>
          <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
          <div class="centum">
            <div class="cell p0 x20">
               <button type="submit" class="btn btn-primary">
               {{ __('Go') }}
               </button>
             </div>
          </div>
        </form>
     </div>
  </div>
@endsection
