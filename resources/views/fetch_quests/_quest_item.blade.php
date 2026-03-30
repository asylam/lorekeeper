<div class="text-center inventory-item" data-id="{{ $item->id }}">
    <div class="mb-1">
        <a href="{{ url('world/items/' . $item->id) }}" class="inventory-stack">
            <img src="{{ $item->imageUrl }}" alt="{{ $item->name }}" height="100" width="100"/>
        </a>
    </div>
    <div>
        <a href="{{ url('world/items/' . $item->id) }}" class="inventory-stack inventory-stack-name">
            <strong>{{ $item->name }}</strong>
        </a>
    </div>
</div>