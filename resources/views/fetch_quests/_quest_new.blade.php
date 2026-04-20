<div class="row text-center">
    @if ($quest->parsed_description)
        <div class="col-12 text-center">
            {!! $quest->parsed_description !!}
        </div>
    @endif
    @if ($quest->has_image)
        <div class="col-12 text-center">
            <div class="shop-image">
                <img src="{{ $quest->FetchQuestImageUrl }}" alt="{{ $quest->name }}" class="mw-100" />
            </div>
        </div>
    @endif
    @if ($quest->greeting_message)
        <div class="col-12 m-3">
            <h5>"{{ $quest->greeting_message }}"</h5>
        </div>
    @endif
</div>
<div class="row text-center">
    <div class="col-12">
        {!! Form::open(['url' => 'fetch-quests/' . $quest->id . '/accept']) !!}
        {!! Form::submit('Accept quest', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
</div>

@section('scripts')
    <script></script>
@endsection
