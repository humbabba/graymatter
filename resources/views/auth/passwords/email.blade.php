@extends('layouts.app')

@section('content')
  <div class="centum">
     <div class="cell x30 center-h">
        <h1 class="pv15">{{ __('Send password-reset link') }}</h1>
        @error('email')
          <div class="alert error">
            {{ $message }}
          </div>
        @enderror
        @if (session('status'))
          <div class="alert success">
              {{ session('status') }}
          </div>
        @endif
        <form class="input-spacing" method="POST" action="{{ route('password.email') }}">
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
