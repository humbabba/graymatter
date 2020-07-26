@extends('layouts.app')

@section('content')
<div>
    <div>
        <div>
            <div>
                <div>
                  <div class="centum ph10">
                    <h1>My stuff</h1>
                  </div>
                  <div class="centum">
                    <div class="cell x60">
                      <p>Welcome.</p>
                      <p><a href="{{ route('info') }}">Info</a></p>
                    </div>
                    <div class="cell x20 spacer-d">
                    </div>
                    <div class="cell x20 p0">
                      <div class="centum">
                        <div class="cell x100 shift+1">
                            <p>Wonderful.</p>
                            <p>Place.</p>
                        </div>
                        <div class="cell x100 p0 border-basic">
                          <div class="cell">
                              <p>Beautiful.</p>
                              <p>Face.</p>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="cell p5 spacer-m">
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
