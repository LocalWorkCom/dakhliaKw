@extends('layout.header')

@push('style')
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />  -->

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
                    <form action="{{ route('Export.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label for="name">العنوان</label>
                            <input type="text" class="form-control" name="name"  id="name" placeholder="العنوان">
                          </div>
                          <div class="form-group col-md-6">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control"  name="num" id="exportnum">
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3"></textarea>
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
                        <div class="form-group">
                            <label for="select-created_by">created_by </label>
                            <select id="select-created_by" name="created_by" class="form-control">
                                <option disabled> اختر من القائمه</option>
                                @foreach ($users as $user )
                                <option value="{{ $user->id }}" >{{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="select-updated_by">updated_by </label>
                            <select id="select-updated_by" name="updated_by" class="form-control" >
                                <option disabled selected> اختر من القائمه</option>
                                @foreach ($users as $user )
                                <option value="{{ $user->id }}" >{{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})</option>
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
                                    <label for="active">الاداره الخارجيه</label>
                                    <select id="active" name="department" >
                                        <option value="1" >اختر الاداره</option>
                                        @foreach ($departments as $department )
                                        <option value="{{ $department->id }}" >{{ $department->name }} </option>
                                        @endforeach
                                  
                                    </select>
                                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                                </div>
                               
                               
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6" >
                                
                                <label for="exampleFormControlFile1"> حمل الملف </label>
                                <input type="file" name="file[]" class="form-control-file" id="file1">

                            </div>
                            <div class="form-group col-md-6" id="fileInputs">
                                
                                <button type="button" id="addFileInput" class="btn btn-primary">Add Another File</button>

                            </div>
                        </div>
                        <div class="form-row">
                             <!-- Button trigger modal -->
                             <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                                أضافه أداره خارجيه
                               </button>
                        </div>
                            <button class="btn btn-primary" type="submit">تعديل </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


  
  <!-- Modal -->
  <div class="modal fade" id="extern-department" tabindex="-1" aria-labelledby="extern-departmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="extern-departmentLabel">إضافة جهة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addRepresentativeForm" action="{{ route('department.ajax') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name">الاسم:</label>
                            <input type="text" id="name" name="name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="desc">الوصف:</label>
                            <input type="text" id="desc" name="desc" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phone" class="form-control">
                        </div>

                        <!-- Save button -->
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="saveExternalDepartment">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}
{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}
 {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> --}}
 {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> --}}

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
         $(document).ready(function () {
            $('#select-person-to').selectize({
                sortField: 'text',
                searchField: 'text',  // Ensure the search field is specified
                create: false  // Optional: Disable creating new items
            });
        });
        $(document).ready(function () {
            $('#select-created_by').selectize({
                sortField: 'text',
                searchField: 'text',  // Ensure the search field is specified
                create: false  // Optional: Disable creating new items
            });
        });
        $(document).ready(function () {
            $('#select-updated_by').selectize({
                sortField: 'text',
                searchField: 'text',  // Ensure the search field is specified
                create: false  // Optional: Disable creating new items
            });
            $('#myModal').on('shown.bs.modal', function () {
  $('#myInput').trigger('focus')
})
        });
    
    </script>
@endpush
