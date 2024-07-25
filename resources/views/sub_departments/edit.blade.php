@extends('layout.main')

@section('title')
تعديل قسم
@endsection
@section('content')
<div class="container">
    <h2>تعديل قسم </h2>
    <form action="{{ route('sub_departments.update', $department->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">اسم القسم </label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $department->name) }}">
        </div>
        <div class="form-group">
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">اختار القسم</option>
                @foreach ($subdepartments as $dept)
                    <option value="{{ $dept->id }}" {{ $department->parent_id == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">تعديل</button>
    </form>
</div>
@endsection
