@extends('layouts.narrow')

@section('content.narrow')
<div class="centum">
  <div class="cell">
    <h1 class="pv15">{{ __('Reset password') }}</h1>
  </div>
  <div class="cell">
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
