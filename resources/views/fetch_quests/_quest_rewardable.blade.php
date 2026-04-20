@if ($questItem?->rewardable)
    @switch($questItem->rewardable_type)
        @case('Item')
            @php $item = $questItem->rewardable; @endphp
            <div class="text-center">
                <div class="inventory-item" data-id="{{ $item->id }}">
                    <div class="mb-1">
                        <a href="{{ url('world/items/' . $item->id) }}" class="inventory-stack">
                            <img src="{{ $item->imageUrl }}" alt="{{ $item->name }}" height="100" width="100" />
                        </a>
                    </div>
                    <div>
                        <a href="{{ url('world/items/' . $item->id) }}" class="inventory-stack inventory-stack-name">
                            <strong>{{ $item->name }}</strong>
                        </a>
                        @if ($questItem->quantity > 1)
                            <div class="text-muted small">x{{ $questItem->quantity }}</div>
                        @endif
                    </div>
                </div>
            </div>
        @break

        @case('Currency')
            @php $currency = $questItem->rewardable; @endphp
            <div class="text-center">
                <div>
                    <h5>{{ $currency->name }}</h5>
                    {!! $currency->display($questItem->quantity) !!}
                </div>
            </div>
        @break

        @default
            <div class="alert alert-warning text-center" role="alert">
                Unknown item type: {{ $questItem->rewardable_type }}
            </div>
    @endswitch
@else
    <div class="alert alert-warning text-center" role="alert">
        Unknown quest item
    </div>
@endif
