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

    <h1>
        @if ($quest->id)
            Editing (<a href='/fetch-quests/{{ $quest->id }}'>{{ $quest->name }}</a>)
            <a href="#" class="btn btn-outline-danger float-right delete-quest-button">Delete Fetch Quest</a>
        @else
            Create Fetch Quest 
        @endif
    </h1>

    {!! Form::open(['url' => $quest->id ? 'admin/data/fetch-quests/edit/' . $quest->id : 'admin/data/fetch-quests/create', 'files' => true]) !!}

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

    <div class="form-group">
        {!! Form::label('Quest Image (Optional)') !!} {!! add_help('This image is used on the quest index and on the quest page as a header.') !!}
        <div class="custom-file">
            {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
            {!! Form::file('image', ['class' => 'custom-file-input']) !!}
        </div>
        <div class="text-muted mb-2">Recommended size: None (Choose a standard size for all shop images)</div>
        @if ($quest->has_image)
            <div class="form-check">
                <div class="shop-image text-center">
                    <a href="{{ $quest->FetchQuestImageUrl }}"><img src="{{ $quest->FetchQuestImageUrl }}" alt="{{ $quest->name }}" style="max-height:200px"/></a>
                </div>
                <div class="text-right mr-3">
                    {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                    {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Description (Optional)') !!}
                {!! Form::textarea('description', $quest->description, ['class' => 'form-control wysiwyg']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('Greeting Message') !!}
                {!! Form::text('greeting_message', $quest->greeting_message, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('Request Message') !!}
                {!! Form::text('request_message', $quest->request_message, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('Completion Message') !!}
                {!! Form::text('completion_message', $quest->completion_message, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('Expired Message') !!}
                {!! Form::text('expired_message', $quest->expired_message, ['class' => 'form-control']) !!}
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
