@extends('layouts.app')

@section('content')
  <div class="centum">
     <div class="cell x30 center-h">
        <h1 class="pv15">{{ __('Confirm password') }}</h1>
        @error('password')
          <div class="alert error" style="display: none">
            {{ $message }}
          </div>
        @enderror
        <p>{{ __('Please confirm your password before continuing.') }}</p>
        <form class="inputspace" method="POST" action="{{ route('password.confirm') }}">
          @csrf
          <label for="email">{{ __('Password') }}</label>
          <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password" autofocus>
          <div class="centum">

          </div>

          <div class="centum">
             <div class="cell p0 x20">
                <button type="submit" class="btn btn-primary">
                {{ __('Go') }}
                </button>
              </div>
              <div class="cell ph0 x80 align-right-d">
                @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}">
                  {{ __('Forgot your password?') }}
                </a>
                @endif
             </div>
          </div>
        </form>
     </div>
  </div>
@endsection
