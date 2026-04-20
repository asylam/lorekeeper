<div class="row text-center">
    @if ($quest->has_image)
        <div class="col-12 text-center">
            <div class="shop-image">
                <img src="{{ $quest->FetchQuestImageUrl }}" alt="{{ $quest->name }}" class="mw-100" />
            </div>
        </div>
    @endif
    @if ($quest->request_message)
        <div class="col-12 m-3">
            <h5>"{{ $quest->request_message }}"</h5>
        </div>
    @endif
    <div class="col-md-6">
        <h3>Request:</h3>
        @if ($activeUserQuest->requestItems->count())
            <div class="row justify-content-center">
                @foreach ($activeUserQuest->requestItems as $questItem)
                    <div class="{{ $activeUserQuest->requestItems->count() == 1 ? 'col-12' : 'col-md-6 col-sm-6 col-xs-12' }}">
                        @include('fetch_quests._quest_rewardable', ['questItem' => $questItem])
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-danger">No request item</div>
        @endif
    </div>
    <div class="col-md-6 text-center">
        <h3>Reward:</h3>
        @if ($activeUserQuest->rewardItems->count())
            <div class="row justify-content-center">
                @foreach ($activeUserQuest->rewardItems as $questItem)
                    <div class="{{ $activeUserQuest->rewardItems->count() == 1 ? 'col-12' : 'col-md-6 col-sm-6 col-xs-12' }}">
                        @include('fetch_quests._quest_rewardable', ['questItem' => $questItem])
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-danger">No reward item</div>
        @endif
    </div>
</div>
<div class="row text-center">
    <div class="col-12">
        Due in {{ now()->diff($activeUserQuest->due_at)->format('%hh %im') }}
    </div>
    <div class="col-12">
        {!! Form::open(['url' => 'fetch-quests/' . $quest->id . '/complete']) !!}
        {!! Form::submit('Complete quest', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
</div>
