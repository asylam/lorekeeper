@extends('fetch_quests.layout')

@section('quest-name')
    Home
@endsection

@section('content')
    {!! breadcrumbs(['Fetch Quests' => 'fetch-quests']) !!}

    <h1>
        Fetch Quests
    </h1>

    <div class="row shops-row">
        @foreach ($quests as $quest)
            <div class="col-md-3 col-6 mb-3 text-center">
                @if ($quest->has_image)
                    <div class="shop-image">
                        <a href="{{ url('fetch-quests/' . $quest->id) }}"><img src="{{ $quest->FetchQuestImageUrl }}" alt="{{ $quest->name }}" style="max-height: 200px" /></a>
                    </div>
                @endif
                <div class="shop-name mt-1">
                    <a href="{{ url('fetch-quests/' . $quest->id) }}" class="h5 mb-0">{{ $quest->name }}</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
