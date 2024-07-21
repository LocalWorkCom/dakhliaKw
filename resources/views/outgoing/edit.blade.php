@extends('layout.header')

@push('style')
@endpush

@section('content')
<div class="row" style="direction: rtl;">
    <nav style="--bs-breadcrumb-divider: '>';" class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
            <li class="breadcrumb-item active"><a href="{{ route('Export.index') }}">الصادرات </a></li>
            <li class="breadcrumb-item active" aria-current="page"> تعديل</li>
        </ol>
    </nav>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> الصـــــــــادرات</p>
    </div>
</div>
<div class="container  col-11 mt-3 p-0 ">
    <div class="row justify-content-end">
        <div class="col-auto">
            <button type="button" class="btn-all wide-btn mt-3" data-bs-toggle="modal" id="extern-department-dev"
                data-bs-target="#extern-department" style="color:#0D992C;">
                اضافه اداره خارجيه <img src="{{ asset('frontend/images/addnew.svg')}}" alt="">
            </button>
        </div>
    </div>
    <div class=" col-lg-12">

        <li class="breadcrumb-item active">اضافه الصادر</li>

    </ol>
    
    <div class="container-fluid" style="text-align: center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-block">
                    @include('inc.flash')
                    <form action="{{ route('Export.update', ['Export' => $data->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label for="nameex">العنوان</label>
                            <input type="text" class="form-control" name="nameex"  id="nameex" placeholder="العنوان" value="{{ $data->name }}" required>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control"  name="num" id="exportnum" value="{{ $data->num }}" required>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="date">تاريخ الصادر </label>
                            <input type="date" id="date" name="date" class="form-control" value="{{ $data->date }}" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3" required> {{ $data->note }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="select-person-to">الشخص الصادر </label>
                            <select id="select-person-to" name="person_to" class="form-control">
                                <option disabled> اختر من القائمه</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if($data->person_to == $user->id) selected @endif>
                                        {{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="active">الحاله</label>
                            <select id="active" name="active" class="form-control">
                                <option value="0" @if($data->active == 0) selected @endif >مفعل</option>
                                <option value="1" @if($data->active == 1) selected @endif>غير مفعل</option>
                          
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="from_departement">الجهة الصادر منها:</label>
                            <select id="from_departement" name="from_departement" class="form-control" >
                                <option value="">اختر الجهة</option>
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}" @if($data->department_id == $item->id) selected @endif>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="files">الملفات:</label>
                            <div id="fileInputs">
                                <div class="file-input mb-3">
                                    <input type="file" name="files[]" class="form-control-file" >
                                    <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm mt-2" id="addFile">إضافة ملف جديد</button>
                        </div>
                        <div class="form-row">
                            
                            <div class="form-group col-md-6">
                                 <!-- Button trigger modal -->
                                 <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                         id="extern-department-dev" data-bs-target="#extern-department">
                                     أضافه أداره خارجيه
                                 </button>
                            </div>
                         
 
                         </div>

                            {{-- model for add files --}}
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
                                                            <input type="file" name="files[]" class="form-control-file" >
                                                            <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm mt-2" id="addFile">إضافة ملف جديد</button>

                                                <!-- Save button -->
                                                {{-- <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">حفظ</button>
                                                </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                         {{-- endmodel --}}
                       
                            <button class="btn btn-primary" type="submit">تعديل </button>
                    </form>
        @include('inc.flash')
        <form action="{{ route('Export.update', ['Export' => $data->id]) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nameex">العنوان</label>
                    <input type="text" class="form-control" name="nameex" id="nameex" placeholder="العنوان"
                        value="{{ $data->name }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="exportnum">رقم الصادر</label>
                    <input type="text" class="form-control" name="num" id="exportnum" value="{{ $data->num }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="select-person-to">person_to </label>
                    <select id="select-person-to" name="person_to" class="form-control">
                        <option disabled> اختر من القائمه</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}" @if($data->person_to == $user->id) selected @endif>
                            {{ $user->username }} (الرقم العسكرى : {{ $user->military_number }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="from_departement">الجهة المرسلة:</label>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" style="display: none"
                        id="extern-department-dev" data-bs-target="#extern-department">
                        <i class="fa fa-plus"></i>
                    </button>
                    <select id="from_departement" name="from_departement" class="form-control" required>
                        <option value="">اختر الجهة</option>
                        @foreach ($departments as $item)
                        <option value="{{ $item->id }}" @if($data->department_id == $item->id) selected
                            @endif>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="active">الحاله</label>
                    <select id="active" name="active">
                        <option value="0" @if($data->active == 0) selected @endif >مفعل</option>
                        <option value="1" @if($data->active == 1) selected @endif>غير مفعل</option>

                    </select>
                </div>
                <div class="form-group col-md-6">
                <label for="exampleFormControlTextarea1">اختر التاريخ </label>
               <input type="date">
            </div>
               
            </div>
            <div class="form-group">
                <label for="exampleFormControlTextarea1">ملاحظات </label>
                <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3"
                    required> {{ $data->note }}</textarea>
            </div>
            <div class="form-row">
                @if(!( $is_file))
                <div class="form-group col-md-6">

                    <label for="exampleFormControlFile1"> حمل الملف </label>
                    <input type="file" name="files[]" class="form-control-file" id="file1">

                </div>
                <div class="form-group col-md-6" id="fileInputs">

                    <button type="button" id="addFileInput" class="btn btn-primary">Add Another
                        File</button>

                </div>
                @else
                <img src="path/to/your/image.jpg" alt="Image Preview" class="small-image">
                <iframe src="path/to/your/document.pdf" class="small-pdf"></iframe>
                @endif
            </div>

            <button class="btn btn-primary" type="submit">تعديل </button>
        </form>
    </div>
</div>
</div>


<div class="modal fade" id="extern-department" tabindex="-1" aria-labelledby="extern-departmentLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extern-departmentLabel">إضافة جهة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="saveExternalDepartment" action="{{ route('department.ajax') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name">الاسم:</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="desc">الوصف:</label>
                        <input type="text" id="desc" name="desc" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone">الهاتف</label>
                        <input type="text" id="phone" name="phone" class="form-control" required>
                    </div>

                    <!-- Save button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </section>
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