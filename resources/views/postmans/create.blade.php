@extends('layout.main')
@section('title')
أضافه مندوب
@endsection
@section('content')
<main>
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="{{ route('home') }}">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">الادارات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('postmans.create') }}">
                        اضافة مندوب </a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> المندوب </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
        <div class="container col-10 mt-5 mb-5 pb-5" style="border:0.5px solid #C7C7CC;">
            <form action="{{ route('postmans.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row mx-md-3 mt-4 d-flex justify-content-center">
                    <div class="form-group col-md-5 mx-md-2">
                        <label for="department_id">الادارة التابعه</label>
                        <select name="department_id" class="form-control" required>
                            <option value="">اختر الادارة</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-5 mx-md-2">
                        <label for="name">الاسم</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row mx-md-3 d-flex justify-content-center">
                <div class="form-group col-md-5 mx-md-2">
                <label for="phone2">الهاتف الثانى</label>
                    <input type="phone" class="form-control" id="phone2" name="phone2" value="{{ old('phone2') }}" required>
                    @error('phone2')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror



                </div>
                <div class="form-group col-md-5 mx-md-2">
                    <label for="phone1">الهاتف الاول</label>
                    <input type="phone" class="form-control" id="phone1" name="phone1" value="{{ old('phone1') }}" required>
                    @error('phone1')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                </div>
                <div class="form-row mx-md-2 d-flex justify-content-center">
                <div class="form-group col-md-10 ">
                <label for="national_id">رقم الهوية</label>
                    <input type="text" class="form-control" id="national_id" name="national_id"
                        value="{{ old('national_id') }}" required>
                    @error('national_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                </div>  </div>
                <div class="container col-10 mt-5 mb-5 " >
                <div class="form-row col-10 " dir="ltr">
                    <button class="btn-blue " type="submit">
                        اضافة </button>
                </div>   </div>
                <br>
            </form>
        </div>
        </div>
    </div>
    @endsection
