@extends('layout.main')

@section('title')
أضافه قسم
@endsection
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
                <option value="" {{ is_null($parentDepartment) ? 'selected' : '' }} >اختار القسم</option>
                @foreach ($subdepartments as $department)
                    <option value="{{ $department->id }}">
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">اضافة</button>
    </form>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> الــــــــــادارات </p>
    </div>
</div>
<br>
<div class="row">
    <div class="container  col-11 mt-3 p-0 ">
        <div class="container col-10 mt-5 mb-5 pb-5" style="border:0.5px solid #C7C7CC;">
            <h2>اضافة قسم جديد</h2>
            <form action="{{ route('sub_departments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row mx-2 d-flex justify-content-center">
                    <div class="form-group col-md-10">
                        <label for="name">اسم القسم </label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                </div>
                <div class="form-row mx-2 d-flex justify-content-center">
                    <div class="form-group col-md-10">
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="{{$parentDepartment->id}}">اختار القسم</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->parent_id }}">
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
        </div>
        <div class="container col-10 ">
        <div class="form-row mt-5 mb-5">
        <button type="submit" class="btn-blue">اضافة</button>
        </div></div>
        </form>
    </div>
    @endsection
