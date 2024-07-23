@extends('layout.main')

@push('style')
@endpush

@section('content')
    <main>
        <div class="row col-11" dir="rtl">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('Export.index') }}">الصادرات </a></li>
                    <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل الصادر</a></li>
                </ol>
            </nav>
        </div>
        <div class="row ">
            <div class="container welcome col-11">
                <p> الصــــــــــــادرات </p>
            </div>
        </div>
        <br>


        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">
                @include('inc.flash')
                <form action="{{ route('Export.update', ['Export' => $data->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row " dir="rtl">
                        <div class="form-group mt-4  mx-2 col-12 d-flex ">
                            <button type="button" class="wide-btn  " data-bs-toggle="modal" id="extern-user-dev"
                                data-bs-target="#extern-user" style="color: #0D992C;">
                                <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                                اضافة شخص صادر خارجى
                            </button>

                            <button type="button" class="btn-all mx-3 " data-bs-toggle="modal" id="extern-department-dev"
                                data-bs-target="#extern-department" style="color: #0D992C;">
                                <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                                اضافة أداره خارجيه
                            </button>
                        </div>
                    </div>
                    <div class="form-row mx-2">
                        <div class="form-group col-md-6">
                            <label for="nameex">العنوان</label>
                            <input type="text" class="form-control" name="nameex" id="nameex" placeholder="العنوان"
                                value="{{ $data->name }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="select-person-to">الشخص الصادر </label>
                            <select id="select-person-to" name="person_to" class="form-control">
                                <option disabled> اختر من القائمه</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if ($data->person_to == $user->id) selected @endif>
                                        {{ $user->username }} (الرقم العسكرى : {{ $user->military_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row mx-2">
                        <div class="form-group  col-md-6">
                            <label for="date">تاريخ الصادر </label>
                            <input type="date" id="date" name="date" class="form-control"
                                value="{{ $data->date }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control" name="num" id="exportnum"
                                value="{{ $data->num }}" required>
                        </div>
                    </div>
                    <div class="form-row mx-2">
                        <div class="form-group col-md-6">
                            <label for="active">الحاله</label>
                            <select id="active" name="active" class="form-control">
                                <option value="0" @if ($data->active == 0) selected @endif>مفعل</option>
                                <option value="1" @if ($data->active == 1) selected @endif>غير مفعل</option>

                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="from_departement">الجهة الصادر منها</label>
                            <select id="from_departement" name="from_departement" class="form-control">
                                <option value="">اختر الجهة</option>
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}" @if ($data->department_id == $item->id) selected @endif>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row mx-2">
                        <div class="form-group col-md-12">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3" required> {{ $data->note }}</textarea>
                        </div>
                    </div>
                    <div class="form-row mx-2" dir="rtl">
                        <div class="form-group col-md-12">
                            <label for="files">الملفات:</label>
                            <div id="fileInputs">
                                <input type="file" name="files[]" class="form-control">
                                <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>

                            </div>
                            <button type="button" class=" btn-all mx-3" id="addFile">إضافة ملف جديد</button>
                        </div>
                    </div>
                    <div class="form-row" dir="rtl">
                        <button class="btn-all mx-3" type="submit" style="color: #0D992C;"> <img
                                src="{{ asset('frontend/images/add-btn.svg') }}" alt="img"> تعديل </button>
                    </div>
                    <br>
                </form>
            </div>
        </div>

    </main>
    <br> <br> <br>

    {{-- model for add new department --}}
    <div class="modal fade" id="extern-department" tabindex="-1" aria-labelledby="extern-departmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة جهة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="saveExternalDepartment" action="{{ route('department.ajax') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="desc">الوصف</label>
                            <input type="text" id="desc" name="desc" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- model for add new user --}}
    <div class="modal fade" id="extern-user" tabindex="-1" aria-labelledby="extern-departmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة شخص صادر جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            &times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="saveExternalUser" action="{{ route('userexport.ajax') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nameus"> الاسم</label>
                            <input type="text" id="nameus" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="military_number">رقم العسكرى:</label>
                            <input type="text" id="military_number" name="military_number" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="filenum">رقم الملف:</label>
                            <input type="text" id="filenum" name="filenum" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="Civil_number">رقم الهويه</label>
                            <input type="text" id="Civil_number" name="Civil_number" class="form-control" required>
                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                let fileInputCount = 1;
                const maxFileInputs = 9;

                $('#addFileInput').click(function() {
                    if (fileInputCount < maxFileInputs) {
                        fileInputCount++;
                        const newFileInput = `
                        <div class="form-group">
                            <label for="file${fileInputCount}">File ${fileInputCount}</label>
                            <input type="file" name="files[]" id="file${fileInputCount}" class="form-control-file">
                        </div>`;
                        $('#fileInputs').append(newFileInput);
                    } else {
                        alert('You can only add up to 10 files.');
                    }
                });
            });
        </script>
    @endpush
