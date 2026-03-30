<ul>
    <li class="sidebar-header">
        <a href="{{ url('fetch-quests') }}" class="card-link">Fetch Quests</a>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Fetch Quests</div>
        @foreach ($quests as $quest)
            <div class="sidebar-item"><a href="{{ url('fetch-quests/' . $quest->id) }}" class="{{ set_active('fetch-quests/' . $quest->id) }}">{{ $quest->name }}</a></div>
        @endforeach
    </li>
</ul>
