@if(session('success'))
    <div class="alert alert-success span-error">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
<div class="alert alert-danger span-error">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif