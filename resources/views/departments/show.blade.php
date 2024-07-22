@extends('layout.main')

@section('content')
<div class="container">
    <h1>Department Details</h1>

    <div class="card">
        <div class="card-header">
            Department #{{ $department->id }}
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $department->name }}</h5>
            <p class="card-text"><strong>Manager:</strong> {{ $department->manager ? $department->manager->id : 'N/A' }}</p>
            <p class="card-text"><strong>Manager Assistant:</strong> {{ $department->managerAssistant ? $department->managerAssistant->id : 'N/A' }}</p>
            <p class="card-text"><strong>Description:</strong> {{ $department->description }}</p>
            <p><strong>Parent Department:</strong> {{ $department->parent ? $department->parent->name : 'None' }}</p>

        </div>
    </div>
    <h2>Child Departments</h2>
    @if($department->children->count() > 0)
        <ul>
            @foreach($department->children as $child)
                <li>{{ $child->name }}</li>
            @endforeach
        </ul>
    @else
        <p>No child departments.</p>
    @endif
    <a href="{{ route('departments.index') }}" class="btn btn-primary mt-3">Back to Departments</a>
</div>
@endsection
