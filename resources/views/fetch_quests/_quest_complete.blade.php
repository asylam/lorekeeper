<h1>
    {{ $quest->name }}
</h1>

<div class="row text-center">
    <div class="col-md-12 text-center">
        <h3>Quest Completed!</h3>
        Resets in {{ now()->diff(now()->endOfDay())->format('%hh %im') }}
    </div>
</div>

@section('scripts')
    <script>
    </script>
@endsection
