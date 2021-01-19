@if(!empty($output->msg))
    @foreach($output->msg as $msg)
        @foreach($msg as $index => $value)
            <div class="centum">
                <div class="cell alert {{ $index }}" style="display: none">
                    {!! $value !!}
                </div>
            </div>
        @endforeach
    @endforeach
@endif
@if(session('success'))
    <div class="centum">
        <div class="cell alert success" style="display: none">
            {!! session('success') !!}
        </div>
    </div>
@endif
@if(session('error'))
    <div class="centum">
        <div class="cell alert error" style="display: none">
            {!! session('error') !!}
        </div>
    </div>
@endif
@if(session('notice'))
    <div class="centum">
        <div class="cell alert notice" style="display: none">
            {!! session('notice') !!}
        </div>
    </div>
@endif
@if ($errors->any())
    <div class="centum">
        <div class="cell alert error" style="display: none">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
