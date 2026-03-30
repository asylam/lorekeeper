@extends('admin.layout')

@section('admin-title')
    Fetch Quest Index
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Fetch Quest Index' => 'admin/fetch-quests']) !!}

    <h1>Fetch Quest Index</h1>
    <div class="text-right form-group">
        <a class="btn btn-success edit-fetch-quest" href="{{ url('admin/data/fetch-quests/create') }}" data-id="">Create Fetch Quest</a>
    </div>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a href="{{ url()->current() }}?is_active=1" class="nav-link {{ Request::get('is_active') == 1 ? 'active' : '' }}">Active</a></li>
        <li class="nav-item"><a href="{{ url()->current() }}?is_active=2" class="nav-link {{ Request::get('is_active') == 2 ? 'active' : '' }}">Inactive</a></li>
        <li class="nav-item"><a href="{{ url()->current() }}" class="nav-link {{ Request::get('is_active') ? '' : 'active' }}">All</a></li>
    </ul>
    @if (Request::get('is_active') == 1)
        <p>This is the list of quests that are visible.</p>
    @elseif(Request::get('is_active') == 2)
        <p>This is the list of quests that are hidden.</p>
    @elseif(!Request::get('is_active'))
        <p>This is the list of all quests, active and inactive.</p>
    @endif
    <ul class="list-group mb-3">
        <div class="card mb-3">
            <ul class="list-group list-group-flush">
                @foreach ($quests as $quest)
                    <li class="list-group-item">
                        <i class="fas {{ $quest->is_active == 1 ? 'fa-eye' : 'fa-eye-slash' }} mr-2"></i>
                        {{ $quest->name }}
                        <div class="float-right">
                            <a href="{{ url('admin/data/fetch-quests/edit/' . $quest->id) }}" class="edit-quest btn btn-xs btn-outline-primary p-2" data-id="{{ $quest->id }}">
                                Edit quest
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </ul>
@endsection
