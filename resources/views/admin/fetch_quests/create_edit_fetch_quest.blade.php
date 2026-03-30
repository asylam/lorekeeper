@extends('admin.layout')

@section('admin-title')
    {{ $quest->id ? 'Edit' : 'Create' }} Fetch Quest
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Fetch Quests' => 'admin/data/fetch-quests?is_active=1',
        ($quest->id ? 'Edit' : 'Create') . ' Fetch Quest' => $quest->id ? 'admin/data/fetch-quests/edit/' . $quest->id : 'admin/data/fetch-quests/create',
    ]) !!}

    <h1>{{ $quest->id ? 'Edit' : 'Create' }} Fetch Quest
        @if ($quest->id)
            <a href="#" class="btn btn-outline-danger float-right delete-quest-button">Delete Fetch Quest</a>
        @endif
    </h1>

    {!! Form::open(['url' => $quest->id ? 'admin/data/fetch-quests/edit/' . $quest->id : 'admin/data/fetch-quests/create']) !!}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-10">
            <div class="form-group">
                {!! Form::label('Name') !!}
                {!! Form::text('name', $quest->name, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('Active') !!}
                <div class="form-group">
                    {!! Form::hidden('is_active', 2) !!}
                    {!! Form::checkbox('is_active', 1, $quest->is_active == 1, ['data-toggle' => 'toggle']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Request Item') !!}
                {!! Form::select('request_item_id', $request_item_id, $quest->request_item_id, ['class' => 'selectize form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('Reward Item') !!}
                {!! Form::select('reward_item_id', $reward_item_id, $quest->reward_item_id, ['class' => 'selectize form-control']) !!}
            </div>
        </div>
    </div>
    <div class="text-right">
        {!! Form::submit($quest->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.selectize').selectize();

            $('.delete-quest-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/fetch-quests/delete') }}/{{ $quest->id }}", 'Delete Fetch Quest');
            });
        });
    </script>
@endsection
