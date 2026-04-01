<div class="row text-center">
    <div class="col-md-12 text-center mb-4">
        <h3>Quest Completed!</h3>
        Resets in {{ now()->diff(now()->endOfDay())->format('%hh %im') }}
    </div>
    @if ($quest->has_image)
        <div class="col-12 text-center">
            <div class="shop-image">
                <img src="{{ $quest->FetchQuestImageUrl }}" alt="{{ $quest->name }}" class="mw-100" />
            </div>
        </div>
    @endif
    @if ($quest->completion_message)
        <div class="col-12 m-3">
            <h5>"{{ $quest->completion_message }}"</h5>
        </div>
    @endif
</div>

@section('scripts')
    <script></script>
@endsection
