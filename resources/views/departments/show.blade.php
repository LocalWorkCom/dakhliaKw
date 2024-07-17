@extends('layout.header')

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
        </div>
    </div>
    
    <a href="{{ route('departments.index') }}" class="btn btn-primary mt-3">Back to Departments</a>
</div>
@endsection
