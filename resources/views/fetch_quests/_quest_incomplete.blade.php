<h1>
    {{ $quest->name }}
</h1>

<div class="row text-center">
    <div class="col-md-6">
        <h3>Request:</h3>
        @include('fetch_quests._quest_item', ['item' => $quest->requestItem, 'isPage' => true])
    </div>
    <div class="col-md-6">
        <h3>Reward:</h3>
        @include('fetch_quests._quest_item', ['item' => $quest->rewardItem, 'isPage' => true])
    </div>
</div>
<div class="row mt-4 text-center">
    <div class="col-12">
        {!! Form::open(['url' => 'fetch-quests/' . $quest->id . '/complete']) !!}
        {!! Form::submit('Complete quest', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
</div>

@section('scripts')
    <script>
    </script>
@endsection
