@extends('layouts.broad')
@section('content.broad')
    <div class="flex justify-center">
      <div class="max-w-[700px]">
          @yield('content.narrow')
      </div>
    </div>
@endsection
