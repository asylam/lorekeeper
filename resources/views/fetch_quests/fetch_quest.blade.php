@extends('fetch_quests.layout')

@section('title')
    {{ $quest->name }}
@endsection

@section('content')
    {!! breadcrumbs(['Fetch Quests' => 'fetch-quests', $quest->name => 'fetch-quests/' . $quest->id]) !!}
    <h1>
        {{ $quest->name }}
    </h1>

    @if (!$activeUserQuest)
        @include('fetch_quests._quest_new', ['quest' => $quest, 'isPage' => true])
    @elseif ($activeUserQuest->past_due)
        @include('fetch_quests._quest_past_due', ['quest' => $quest, 'isPage' => true])
    @elseif ($activeUserQuest->accepted)
        @include('fetch_quests._quest_request', ['quest' => $quest, 'isPage' => true])  
    @else
        @include('fetch_quests._quest_complete', ['quest' => $quest, 'isPage' => true])
    @endif

@endsection
