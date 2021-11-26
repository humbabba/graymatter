@extends('layouts.broad')
@section('content.broad')
    <div class="centum">
        <div class="cell x-max700 center-h p0">
            @yield('content.narrow')
        </div>
    </div>
@endsection
