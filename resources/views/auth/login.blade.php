@extends('layouts.app')
@section('content')
  <div class="centum">
     <div class="cell x50 center">
        <h1 class="pv15">{{ __('Log in') }}</h1>
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
        <form method="POST" action="{{ route('login') }}">
          @csrf
          <label for="email">{{ __('Email address') }}</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
          <label for="password">{{ __('Password') }}</label>
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
          <div class="centum">
            <div class="cell ph0">
              <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="inline" for="remember">{{ __('Remember me') }}</label>
            </div>
          </div>

           <div class="centum">
              <div class="cell p0">
                 <button type="submit" class="btn btn-primary">
                 {{ __('Go') }}
                 </button>
               </div>
               <div class="cell ph0">
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
