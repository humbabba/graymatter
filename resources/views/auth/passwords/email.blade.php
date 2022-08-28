@extends('layouts.narrow')

@section('content.narrow')
<div class="centum">
  <div class="cell">
    <h1>{{ __('Send password-reset link') }}</h1>
  </div>
  <div class="cell x-max350">
    <form class="inputspace" method="POST" action="{{ route('password.email') }}">
      @csrf
      <label for="email">{{ __('Email address') }}</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
      <button type="submit" class="btn btn-primary">
        {{ __('Go') }}
      </button>
    </form>
  </div>
</div>
@endsection
