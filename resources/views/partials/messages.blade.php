@foreach(config('constants.message_types') as $type)
    {{-- First we handle flashed session data from redirects --}}
    @if(session($type))
        <div class="centum">
            <div class="cell alert {{$type}}" style="display: none">
                {!! session($type) !!}
            </div>
        </div>
    @endif
    {{-- Then variables returned with views --}}
    @if (!empty($$type))
        <div class="cell alert {{$type}}" style="display: none">
            {!! ${$type} !!}
        </div>
    @endif
@endforeach
{{-- Now for error bag --}}
@if($errors->any())
    @foreach ($errors->all() as $error)
        <div class="cell alert error" style="display: none">
            {{ $error }}
        </div>
    @endforeach
@endif
