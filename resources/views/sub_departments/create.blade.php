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
            <label for="parent_id">اختر القسم الرئيسي</label>
            <select class="form-control" id="parent_id" name="parent_id">
                <option value="">قسم رئيسي</option>
                @foreach($departments as $department)
                
                    @include('departments.partials.department-option', ['department' => $department, 'level' => 0])
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">اضافة</button>
    </form>
</div>
@endsection
