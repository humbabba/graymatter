@extends('layouts.app')

@section('content')
  <div class="centum">
     <div class="cell x30 center-h">
        <h1 class="pv15">{{ __('Reset password') }}</h1>
        @error('email')
          <div class="alert error">
            {{ $message }}
          </div>
        @enderror
        @error('password')
          <div class="alert error">
            <strong>{{ $message }}</strong>
          </div>
        @enderror
        <form class="input-spacing" method="POST" action="{{ route('password.update') }}">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">
          <label for="email">{{ __('Email address') }}</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
          <label for="password">{{ __('Password') }}</label>
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
          <label for="password-confirm">{{ __('Confirm password') }}</label>
          <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
           <button type="submit" class="btn btn-primary">
           {{ __('Go') }}
           </button>
        </form>
     </div>
  </div>
@endsection
