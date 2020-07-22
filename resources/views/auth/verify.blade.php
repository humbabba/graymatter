@extends('layouts.app')

@section('content')
  <div class="centum">
     <div class="cell x35 center">
        <h1 class="pv15">{{ __('Verify your email address') }}</h1>
        @if (session('resent'))
          <div class="alert success">
            {{ __('A fresh verification link has been sent to your email address.') }}
          </div>
        @endif
        <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
        <p>{{ __('If you did not receive the email') }},</p>
        <form method="POST" action="{{ route('verification.resend') }}">
          @csrf
          <button type="submit" class="btn btn-primary">
            {{ __('click here to request another') }}
          </button>
        </form>
     </div>
  </div>
@endsection
