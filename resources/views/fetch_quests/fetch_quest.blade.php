@extends('fetch_quests.layout')

@section('title')
    {{ $quest->name }}
@endsection

@section('content')
    {!! breadcrumbs(['Fetch Quests' => 'fetch-quests', $quest->name => 'fetch-quests/' . $quest->id]) !!}
    <h1>
        {{ $quest->name }}
    </h1>
    @if ($quest->completed != 1)
        @include('fetch_quests._quest_incomplete', ['quest' => $quest, 'isPage' => true])
    @else
        @include('fetch_quests._quest_complete', ['quest' => $quest, 'isPage' => true])
    @endif
@endsection
