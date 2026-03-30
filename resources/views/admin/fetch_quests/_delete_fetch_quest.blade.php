@if ($quest)
    {!! Form::open(['url' => 'admin/data/fetch-quests/delete/' . $quest->id]) !!}

    <p>You are about to delete the quest <strong>{{ $quest->name }}</strong>. This is not reversible. This will remove the quest from all users who have it.</p>
    <p>Are you sure you want to delete <strong>{{ $quest->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete quest', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid quest selected.
@endif
