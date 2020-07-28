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
