@extends('layout.main')

@section('content')
<div class="container">
    <h1>Create Department</h1>
    <form action="{{ route('departments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="name">Name </label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="manger">Manager</label>
            <select name="manger" class="form-control">
                <option value="">Select Manager</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @error('manager')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            
        </div>
        <div class="form-group">
            <label for="manger_assistance">Manager Assistant</label>
            <select name="manger_assistance" class="form-control">
                <option value="">Select Manager Assistant</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @error('manger_assistance')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="description">Description </label>
            <input type="text" name="description" class="form-control" value="{{ old('description') }}">
            @error('description')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <h1>Departments</h1>
    <ul>
        @foreach($departments as $department)
            <li>
                {{ $department->name }} (Parent: {{ $department->parent ? $department->parent->name : 'None' }})
                @if($department->children->count())
                    <ul>
                        @foreach($department->children as $child)
                            <li>{{ $child->name }}</li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endsection
