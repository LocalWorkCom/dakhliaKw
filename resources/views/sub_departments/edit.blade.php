@extends('layout.main')


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
                <option value="{{$parentDepartment->id}}">اختار القسم</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->parent_id }}" {{ $department->parent_id == old('parent_id', $department->parent_id) ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">تعديل</button>
    </form>
</div>
@endsection
