@extends('welcome')

@section('content')
<div class="container">
    <h1>Departments</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <a href="{{ route('departments.create') }}" class="btn btn-primary mb-3">Create Department</a>
    @if($departments->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name (Arabic)</th>
                    <th>Manager</th>
                    <th>Manager Assistant</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                    <tr>
                        <td>{{ $department->id }}</td>
                        <td>{{ $department->name }}</td>
                        <td>{{ $department->manager ? $department->manager->id : 'N/A' }}</td>
                        <td>{{ $department->managerAssistant ? $department->managerAssistant->id : 'N/A' }}</td>
                        <td>
                            <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $departments->links() }}
    @else
        <p>No departments found.</p>
    @endif
</div>
@endsection