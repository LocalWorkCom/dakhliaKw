@extends('layout.main')

@section('content')

<main>
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="{{ route('home') }}">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">القطاعات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل علي الادارة </a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> القطاعات </p>
        </div>
    </div>
    <br>


    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="container col-10 mt-5 mb-5 pb-5 pt-5" style="border:0.5px solid #C7C7CC;">
                <form action="{{ route('departments.update', $department->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-row mx-md-3 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="name">اسم الادارة </label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}">
                            @error('name')
                            <div class="alert alert-danger" style="height: 40px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-5 mx-md-2">
                            <label for="manger">المدير</label>
                            <select name="manger" class="form-control">
                                <option value="">اختر المدير </option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == old('manger', $department->manger) ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
          



                    <div class="form-row mx-md-2 d-flex justify-content-center">
                    <div class="form-group col-md-10">
                        <label for="description">الوصف </label>
                        <input type="text" name="description" class="form-control"  value="{{ old('description', $department->description) }}">
                        @error('description')
                            <div class="alert alert-danger" style="height: 40px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            </div>
             <div class="container col-10 ">
                <div class="form-row mt-1 mb-3">
                    <button class="btn-blue " type="submit" dir="rtl">
                        <img class="px-1" src="../images/edit.svg" alt="">تعديل

                    </button>
                </div>
        </div>
        <br><br>
        </form>
    </div>
    </div>
    <!-- **********modal******** -->
    <div class="modal fade" id="addFile" tabindex="-1" aria-labelledby="extern-departmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="extern-departmentLabel">إضافة ملفات جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="mb-3">
                            <label for="files">حمل الملفات</label>
                            <div id="fileInputs">
                                <div class="file-input mb-3">
                                    <input type="file" name="files[]" class="form-control-file">
                                    <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn  btn-sm mt-2" id="addFile">إضافة ملف
                        جديد</button>

                    <!-- Save button -->
                    {{-- <div class="text-end">
                                <button type="submit" class="btn btn-primary">حفظ</button>
                            </div> --}}
                </div>
            </div>
        </div>
    </div>
</main>

<!-- <div class="container">
    <h1>Edit Department</h1>
    <form action="{{ route('departments.update', $department->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name </label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}">
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="manger">Manager</label>
            <select name="manger" class="form-control">
                <option value="">Select Manager</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == old('manger', $department->manger) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            @error('manger')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="manger_assistance">Manager Assistant</label>
            <select name="manger_assistance" class="form-control">
                <option value="">Select Manager Assistant</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == old('manger_assistance', $department->manger_assistance) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            @error('manger_assistance')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="description">Description </label>
            <input type="text" name="description" class="form-control"  value="{{ old('name', $department->description) }}">
            @error('description')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div> -->
@endsection
