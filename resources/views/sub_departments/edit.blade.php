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
<div class="row col-11" dir="rtl">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="{{ route('home') }}">الرئيسيه</a></li>
            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">الادارات </a></li>
            <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('departments.create') }}">
                    اضافه اداره</a></li>
        </ol>
    </nav>
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
            <form action="{{ route('sub_departments.update', $department->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row mx-2 d-flex justify-content-center">
                    <div class="form-group col-md-10">
                        <label for="name">اسم القسم </label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $department->name) }}">
                    </div>
                </div>

                <div class="form-row mx-2 d-flex justify-content-center">
                    <div class="form-group col-md-10">
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="{{$parentDepartment->id}}">اختار القسم</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->parent_id }}" {{ $department->parent_id == old('parent_id', $department->parent_id) ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
        </div>
        <div class="container col-10 ">
            <div class="form-row mt-5 mb-5">
                <button type="submit" class="btn btn-primary">تعديل</button>
            </div>
        </div>
        </form>
    </div>
    @endsection
