@extends('layout.main')

@section('content')
<div class="container">
    <h1>Edit Department</h1>
    <form action="{{ route('departments.update', $department->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name </label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}">
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="manger">Manager</label>
            <select name="manger" class="form-control">
                <option value="">Select Manager</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == old('manger', $department->manger) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            
            @error('manger')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="manger_assistance">Manager Assistant</label>
            <select name="manger_assistance" class="form-control">
                <option value="">Select Manager Assistant</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == old('manger_assistance', $department->manger_assistance) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            @error('manger_assistance')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="description">Description </label>
            <input type="text" name="description" class="form-control"  value="{{ old('name', $department->description) }}">
            @error('description')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
