@extends('layouts.narrow')

@section('content.narrow')
<div class="centum">
  <div class="cell">
    <h1>{{ __('Register') }}</h1>
  </div>

  <div class="cell x-max350">
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
        <button type="submit" class="btn btn-primary">
          {{ __('Go') }}
        </button>
    </form>
  </div>
</div>
@endsection
