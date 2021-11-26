@extends('layouts.narrow')

@section('content.narrow')
  <div class="centum">
     <div class="cell x-max700 center-h">
        <h1 class="pv15">{{ __('Verify your email address') }}</h1>
        @if (session('resent'))
          <div class="alert success" style="display: none">
            {{ __('A fresh verification link has been sent to your email address.') }}
          </div>
        @endif
        <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
        <form method="POST" action="{{ route('verification.resend') }}">
          @csrf
          <button type="submit" class="btn btn-primary">
            {{ __('Click here to request another link') }}
          </button>
        </form>
     </div>
  </div>
@endsection
