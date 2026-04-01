<div class="row text-center">
    <div class="col-md-12 text-center mb-4">
        <h4>Time has run out!</h4>
    </div>
    @if ($quest->has_image)
        <div class="col-12 text-center">
            <div class="shop-image">
                <img src="{{ $quest->FetchQuestImageUrl }}" alt="{{ $quest->name }}" class="mw-100"/>
            </div>
        </div>
    @endif
    @if ($quest->expired_message)
        <div class="col-12 m-3">
            <h5>"{{ $quest->expired_message }}"</h5>
        </div>
    @endif
</div>
<div class="row text-center">
    <div class="col-12">
        {!! Form::open(['url' => 'fetch-quests/' . $quest->id . '/abandon']) !!}
        {!! Form::submit('Abandon Quest', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
</div>