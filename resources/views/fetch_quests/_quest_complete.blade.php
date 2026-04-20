<div class="row text-center">
    <div class="col-md-12 text-center mb-4">
        <h3>Quest {{ $activeUserQuest->completed ? 'Completed!' : 'Expired...' }}</h3>
        Resets in {{ now()->diff(now()->endOfDay())->format('%hh %im') }}
    </div>
    @if ($quest->has_image)
        <div class="col-12 text-center">
            <div class="shop-image">
                <img src="{{ $quest->FetchQuestImageUrl }}" alt="{{ $quest->name }}" class="mw-100" />
            </div>
        </div>
    @endif
    @if ($activeUserQuest->completed && $quest->completion_message)
        <div class="col-12 m-3">
            <h5>"{{ $quest->completion_message }}"</h5>
        </div>
    @elseif ($activeUserQuest->expired && $quest->expired_message)
        <div class="col-12 m-3">
            <h5>"{{ $quest->expired_message }}"</h5>
        </div>
    @endif
</div>
