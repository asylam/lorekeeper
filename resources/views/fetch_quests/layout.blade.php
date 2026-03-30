@extends('layouts.app')

@section('title')
    Fetch Quest{!! View::hasSection('quest-title') ? ' :: ' . trim(View::getSection('quest-name')) : '' !!}
@endsection

@section('sidebar')
    @include('fetch_quests._sidebar')
@endsection

@section('content')
    @yield('quest-content')
@endsection

@section('scripts')
    @parent
@endsection
