@extends('layout.main')

@push('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
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
                            <label for="name">العنوان</label>
                            <input type="text" class="form-control" name="name"  id="name" placeholder="العنوان" required>
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
                            <select class="selectpicker" data-live-search="true">
                                <option data-tokens="ketchup mustard">Hot Dog, Fries and a Soda</option>
                                <option data-tokens="mustard">Burger, Shake and a Smile</option>
                                <option data-tokens="frosting">Sugar, Spice and all things nice</option>
                              </select>
                              
                              
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
                                    <label for="active">الاداره الخارجيه</label>
                                    <select id="department_id" name="department" >
                                        <option value="">اختر الاداره</option>
                                        @foreach ($departments as $department )
                                        <option value="{{ $department->id }}" >{{ $department->name }} </option>
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
                                    id="extern-department-dev" style="display: none" data-bs-target="#extern-department">
                                أضافه أداره خارجيه
                            </button>
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
                                                {{-- <form id="addRepresentativeForm" action="{{ route('department.ajax') }}" method="POST">
                                                    @csrf --}}
                            
                                                    <div class="mb-3">
                                                        <label for="name_depart">الاسم:</label>
                                                        <input type="text" id="name_depart" name="name_depart" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="description_depart">الوصف:</label>
                                                        <input type="text" id="description_depart" name="description_depart" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="phone_depart">الهاتف</label>
                                                        <input type="text" id="phone_depart" name="phone_depart" class="form-control">
                                                    </div>
                            
                                                    <!-- Save button -->
                                                    <div class="text-end">
                                                        <button type="button" class="btn btn-primary" id="saveExternalDepartment">حفظ</button>
                                                    </div>
                                                {{-- </form> --}}
                                            </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-primary" type="submit">تعديل </button>

                    </form>
                </div>
            </div>
        </div>
    </div>


  
 
</section>
@endsection

@push('scripts')
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>

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
            $('.selectpicker').selectpicker();
            $('#select-person-to').selectize({
                sortField: 'text',
                searchField: 'text',  // Ensure the search field is specified
                create: false  // Optional: Disable creating new items
            });
        });
        // $(document).ready(function () {
        //     $('#select-created_by').selectize({
        //         sortField: 'text',
        //         searchField: 'text',  // Ensure the search field is specified
        //         create: false  // Optional: Disable creating new items
        //     });
        // });
        
        // $(document).ready(function () {
        //     $('#select-updated_by').selectize({
        //         sortField: 'text',
        //         searchField: 'text',  // Ensure the search field is specified
        //         create: false  // Optional: Disable creating new items
        //     });
        $(document).ready(function() {
    // Show/hide elements based on the checked input type
    $('input[name=type]').click(function() {
        if ($(this).is(':checked')) {
            var value = $(this).val();
            console.log(value);
            if (value == 'intern') {
                $('#department_id').show();
                $('#extern-department-dev').hide();
            } else {
                $('#department_id').hide();
                $('#extern-department-dev').show();
            }
        }
    });

    // Show the modal when the button is clicked
    $('#extern-department-dev').click(function() {
        $('#extern-department').modal('show');
    });

    // Handle the form submission inside the modal
    $('#saveExternalDepartment').click(function() {
        var name = $('#name').val();
        var desc = $('#desc').val();
        var phone = $('#phone').val();

        $.ajax({
            url: '{{ route('department.ajax') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: name,
                desc: desc,
                phone: phone
            },
            success: function(response) {
                if (response.success) {
                    $('#extern-department').modal('hide');
                    // Assuming you want to add the new department to the select options
                    $('#department_id').append(`<option value="${response.id}">${name}</option>`);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
});

        });
    
    </script>
@endpush
