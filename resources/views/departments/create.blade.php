@extends('layout.main')
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('title')
    الأدارات
@endsection
@section('content')
    <main>
        <div class="row " dir="rtl">
            <div class="container  col-11" style="background-color:transparent;">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item "><a href="{{ route('home') }}">الرئيسيه</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">الأدارات </a></li>
                        <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('departments.create') }}">
                                اضافة أداره</a></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row ">
            <div class="container welcome col-11">
                <p> الادارات </p>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">
                <form action="{{ route('departments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="container col-10 mt-5 mb-3 pb-5" style="border:0.5px solid #C7C7CC;">

                        <div class="form-row mx-3 mt-4 d-flex justify-content-center">

                            <div class="form-group col-md-5 mx-md-2">
                                <label for="mangered">المدير</label>
                                <select name="manger" id="mangered"
                                    class=" form-control custom-select custom-select-lg mb-3 select2 "
                                    style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;" required>
                                    <option value="" selected disabled>اختار المدير</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach

                                </select>
                                @error('manger')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-5 mx-md-2">
                                <label for="name">اسم القطاع </label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row mx-2 d-flex justify-content-center">

                            <div class="form-group col-md-10 mx-md-2">
                                <label for="description">الوصف </label>
                                <input type="text" name="description" class="form-control"
                                    value="{{ old('description') }}">
                                @error('description')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-10 mx-md-2">
                                <label for="employess">الموظفين </label>
                                <select name="employess[]" id="employess" multiple
                                    class=" form-control custom-select custom-select-lg mb-3 select2 col-12"
                                    style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;">
                                    <option value=""disabled>اختر موظفين </option>
                                    @foreach ($employee as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="container col-10 mt-5 mb-3 ">
                        <div class="form-row col-10 " dir="ltr">
                            <button class="btn-blue " type="submit">
                                اضافة </button>
                        </div>
                    </div>
                    <br>

                </form>
            </div>



        </div>



        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


    <script>
        $('.select2').select2({
            dir: "rtl"
        });
    </script>
@endsection
