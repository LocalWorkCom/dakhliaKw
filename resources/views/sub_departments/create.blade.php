@extends('layout.main')


@section('content')
<div class="container">
    <h2>اضافة قسم جديد</h2>
    <form action="{{ route('sub_departments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="name">اسم القسم </label>
            <input type="text" class="form-control" id="name" name="name" >
        </div>
        <div class="form-group">
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="{{$parentDepartment->id}}">اختار القسم</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->parent_id }}">
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">اضافة</button>
    </form>
</div>
@endsection
