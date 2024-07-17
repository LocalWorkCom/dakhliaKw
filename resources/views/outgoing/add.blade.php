@extends('layout.header')

@push('style')
@endpush

@section('content')
<section style="direction: rtl;">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('Export.index') }}">الصادرات </a></li>

        <li class="breadcrumb-item active">اضافه الصادر</li>

    </ol>
    
    <div class="container-fluid" style="text-align: center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-block">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                    <form action="{{ route('Export.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label for="nameex">العنوان</label>
                            <input type="text" class="form-control" name="nameex"  id="nameex" placeholder="العنوان" required>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control"  name="num" id="exportnum" required>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3" required> </textarea>
                        </div>
                        <div class="form-group">
                            <label for="select-person-to">person_to </label>
                            <select id="select-person-to" name="person_to" class="form-control">
                                <option disabled> اختر من القائمه</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                       
                        <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="active">الحاله</label>
                                    <select id="active" name="active" >
                                        <option value="1" >مفعل</option>
                                        <option value="0">غير مفعل</option>
                                  
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                <div class="mb-3">
                                        <label for="from_departement">الجهة المرسلة:</label>
                                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" style="display: none" id="extern-department-dev"
                                            data-bs-target="#extern-department">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <select id="from_departement" name="from_departement" class="form-control" required>
                                            <option value="">اختر الجهة</option>
                                            @foreach ($departments as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                </div>
                               
                               
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6" >
                                
                                <label for="exampleFormControlFile1"> حمل الملف </label>
                                <input type="file" name="files[]" class="form-control-file" id="file1">

                            </div>
                            <div class="form-group col-md-6" id="fileInputs">
                                
                                <button type="button" id="addFileInput" class="btn btn-primary">Add Another File</button>

                            </div>
                        </div>
                        <div class="form-row">
                             <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                    id="extern-department-dev" data-bs-target="#extern-department">
                                أضافه أداره خارجيه
                            </button>
                        </div>
                            <button class="btn btn-primary" type="submit">تعديل </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="extern-department" tabindex="-1" aria-labelledby="extern-departmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="extern-departmentLabel">إضافة جهة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addRepresentativeForm" action="{{ route('department.ajax') }}" method="POST">
                    @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name">الاسم:</label>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="desc">الوصف:</label>
                        <input type="text" id="desc" name="desc" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="phone">الهاتف:</label>
                        <input type="text" id="phone" name="phone" class="form-control">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" id="saveExternalDepartment">حفظ</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div> --}}
  
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
