<div class="row text-center">
  @if ($quest->has_image)
      <div class="col-12 text-center">
          <div class="shop-image">
              <img src="{{ $quest->FetchQuestImageUrl }}" alt="{{ $quest->name }}" class="mw-100"/>
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
      @include('fetch_quests._quest_item', ['item' => $quest->requestItem, 'isPage' => true])
  </div>
  <div class="col-md-6">
      <h3>Reward:</h3>
      @include('fetch_quests._quest_item', ['item' => $quest->rewardItem, 'isPage' => true])
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