@extends('layout.main')

@section('title')
أضافه قسم
@endsection
@section('content')
<div class="container">
    <h2>اضافة ادارة جديد</h2>
    <form action="{{ route('sub_departments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="name">اسم الادارة </label>
            <input type="text" class="form-control" id="name" name="name" required>
            @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
        </div>
        <div class="form-group">
        <label for="manger">المدير</label>
                        <select name="manger" class="form-control " required>
                            <option value="">اختار المدير</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('manger')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
        </div>
        <div class="form-group">
            <select name="parent_id" id="parent_id" class="form-control" required>
                <option value="" {{ is_null($parentDepartment) ? 'selected' : '' }} >اختار الادارة</option>
                @foreach ($subdepartments as $department)
                    <option value="{{ $department->id }}">
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
        </div>
        <button type="submit" class="btn btn-primary">اضافة</button>
    </form>
</div>

@endsection
