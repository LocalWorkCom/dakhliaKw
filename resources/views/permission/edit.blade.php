@extends('layout.main')

@section('content')
@section('title')
    تعديل
@endsection
<section>
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="#">الصلاحيات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="#"> تعديل صلاحية</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> الصـــلاحيات </p>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            {{-- {{ dd($models) }} --}}
            <div class="">
                <form action="" method="POST">
                    @csrf
                    <div class="container col-10 mt-4 p-4" style="border:0.5px solid #C7C7CC;">
                    <div class="form-row mx-md-2 d-flex justify-content-center">
                    <div class="form-group  col-md-10 ">
                            <label for="input1"> الصلاحية</label>
                            <select class="custom-select custom-select-lg mb-3" name="name">
                                <option selected disabled>اختر الصلاحية</option>
                                <option value="view" {{ $permissionAction == 'view' ? 'selected' : '' }}>عرض</option>
                                <option value="edit" {{ $permissionAction == 'edit' ? 'selected' : '' }}>تعديل</option>
                                <option value="create" {{ $permissionAction == 'create' ? 'selected' : '' }}>اضافة</option>
                                <option value="delete" {{ $permissionAction == 'delete' ? 'selected' : '' }}>ازالة</option>
                                <option value="download" {{ $permissionAction == 'download' ? 'selected' : '' }}>تحميل</option>
                                <option value="archive" {{ $permissionAction == 'archive' ? 'selected' : '' }}>ارشفة</option>
                                <option value="add_archive" {{ $permissionAction == 'add_archive' ? 'selected' : '' }}> اضافة ارشفة</option>
                            </select>
                        </div> </div>

                        <div class="form-row mx-md-2 d-flex justify-content-center">
                        <div class="form-group  col-md-10 ">
                            <label for="input15">القسم</label>
                            <select id="input15" name="model" class="form-control" placeholder="القسم">
                                @foreach ($models as $item)
                                <option value="{{ $item}}" {{ $permission->guard_name == $item ? 'selected' : '' }}> {{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </div>
                    <!-- Save button -->
                    <div class="container col-10 ">
                        <div class="form-row mt-5 mb-2">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </div>

                    <br>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
