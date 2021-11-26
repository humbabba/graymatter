@extends('layouts.broad')

@section('view_title','Loggers')

@section('content.broad')

    <div class="centum">

        <div class="cell">
            <h1>@yield('view_title') - ({{ $output->total }})</h1>
        </div>

        {{-- Begin search filters --}}
        <div class="cell">
            <form action="" method="get">
                <div class="centum border-basic">
                    <div class="cell x20">
                        <input type="search" name="search" placeholder="User, notes, model ID" value="{{ $output->search }}"/>
                    </div>
                    <div class="cell x20">
                        <select name="model">
                            <option value="">Filter by model</option>
                            @foreach(config('constants.models') as $model)
                                <option value="{{ $model }}" {{ ($output->model === $model)? ' selected':'' }}>{{ $model }}</option>
                            @endforeach
                            <option value="">Clear filter</option>
                        </select>
                    </div>
                    <div class="cell x20">
                        <select name="action">
                            <option value="">Filter by action</option>
                            @foreach(config('constants.actions') as $action)
                                <option value="{{ $action }}" {{ ($output->action === $action)? ' selected':'' }}>{{ $action }}</option>
                            @endforeach
                            <option value="">Clear filter</option>
                        </select>
                    </div>
                    <div class="cell x40 p0">
                        <div class="centum">
                            <div class="cell x10 align-right-d">
                                <span class="center-v"><label>From</label></span>
                            </div>
                            <div class="cell x40">
                                <input type="date" name="from" value="{{ $output->from }}"/>
                            </div>
                            <div class="cell x10 align-right-d">
                                <span class="center-v"><label>To</label></span>
                            </div>
                            <div class="cell x40">
                                <input type="date" name="to" value="{{ $output->to }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="centum">
                        <div class="cell btn-wrap pt0 pb10">
                            <input type="submit" class="btn" value="Search"/>
                            <a class="btn" href="{{ route('loggers.index') }}">Clear</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        {{-- End search filters --}}

        <div class="cell p0">
            <div class="centum">
                <div class="cell x20 header-d">
                    User <span class="sorters" data-key="username"></span>
                </div>
                <div class="cell x15 header-d">
                    Model <span class="sorters" data-key="model"></span>
                </div>
                <div class="cell x15 header-d">
                    Action <span class="sorters" data-key="action"></span>
                </div>
                <div class="cell x20 header-d">
                    Timestamp <span class="sorters" data-key="created_at"></span>
                </div>
                <div class="cell x30 header-d">
                    Notes
                </div>
            </div>
        </div>
        <div class="cell pt0">
            @foreach($output->loggers as $logger)
                <div class="centum striped-odd">
                    <div class="cell x20">
                        {!! renderLinkedUserDisplayName($logger->user_id) !!}
                    </div>
                    <div class="cell x15">
                        @if(!empty($logger->model_link))
                            <a href="{{ $logger->model_link }}">
                        @endif
                            {{ $logger->model }} (ID: {{ $logger->model_id }})
                        @if(!empty($logger->model_link))
                            </a>
                        @endif
                    </div>
                    <div class="cell x15">
                        {{ $logger->action }}
                    </div>
                    <div class="cell x20">
                        {{ $logger->created_at }}
                    </div>
                    <div class="cell x30">
                        {!! $logger->notes !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
