@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@push('style')
    <style>
        .selected-option {
            background-color: #e7e7e7;
            color: black;
        }
    </style>
@endpush
@section('title')
    أنواع المخالفات
@endsection
@section('content')
    <section>
        <div class="row">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> أنواع المخالفـــات</p>
                    {{-- @if (Auth::user()->hasPermission('create VacationType')) --}}
                    <button type="button" class="btn-all  " onclick="openadd()" style="color: #0D992C;">

                            اضافة مخالفه  <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                        </button>
                    {{-- @endif --}}
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 pt-5 pb-4">
                <div class="col-lg-12">
                    <div class="bg-white ">
                        @if (session()->has('message'))
                            <div class="alert alert-info">
                                {{ session('message') }}
                            </div>
                        @endif
                        <div>
                            <table id="users-table"
                                class="display table table-responsive-sm  table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>نوع المخالفه</th>

                                        <th style="width:150px;">العمليات</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
    {{-- this for add form --}}
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center ">
                        <h5 class="modal-title"> اضافة مخالفه </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form class="edit-grade-form" id="add-form" action=" {{ route('violations.store') }}" method="POST">
                        @csrf
                        <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                            <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                <div class="form-group mt-4 mb-3">
                                    <label class="d-flex justify-content-start pt-3 pb-2" for="name"
                                        style=" flex-direction: row-reverse;"> اسم
                                        المخالفه</label>
                                    <input type="text" id="nameadd" name="nameadd" class="form-control"
                                        placeholder="اسم المخالفه" dir="rtl" required>
                                    @if ($errors->has('nameadd'))
                                        <span class="text-danger span-error" id="nameadd-error"
                                            dir="rtl">{{ $errors->first('nameadd') }}</span>
                                    @endif
                                </div>

                                <div class="form-group  mb-3">
                                    <div class="select-wrapper">
                                        <div class="select-box d-flex justify-content-between" id="select-box">
                                            <p> قسم الخاص بالمخالفه</p>
                                            <i class="fa-solid fa-angle-down" style="color: #A3A1A1;"></i>
                                        </div>
                                        <div class="options" id="options">
                                            <div class="search-box">
                                                <input type="text" id="search-input" placeholder="ابحث هنا ....."
                                                    style="width: 100% !important;" dir="rtl">

                                            </div>
                                            @foreach (getDepartments() as $department)
                                                <div class="option" style="    display: flex; justify-content: flex-end;">
                                                    <label for="option{{ $department->id }}"> {{ $department->name }}
                                                    </label>
                                                    <input type="checkbox" id="option{{ $department->id }}"
                                                        value="{{ $department->id }}" name="types[]">

                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                    @if ($errors->has('types'))
                                        <span class="text-danger span-error" id="types-error"
                                            dir="rtl">{{ $errors->first('types') }}</span>
                                    @endif
                                    <div id="selected-values" class="mt-2"></div>
                                </div>
                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2" dir="rtl">
                                    <button type="submit" class="btn-all mx-2 p-2"
                                        style="background-color: #274373; color: #ffffff;" id="openSecondModalBtn">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> اضافة
                                    </button>
                                    <button type="button" class="btn-all p-2"
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                    <!-- Second Modal Body (Initially Hidden) -->
                    <div id="secondModalBody" class="d-none">
                        <div class="body-img-modal d-block mb-4">
                            <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                            <p>تمت الاضافه بنجاح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- this for edit form --}}
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title">تعديل المخالفه</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <form class="edit-grade-form" id="edit-form" action="{{ route('violations.update') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" id="idedit" name="id">
                        <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                            <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                <div class="form-group mt-4 mb-3">
                                    <label class="d-flex justify-content-start pt-3 pb-2" for="nameedit"
                                        style=" flex-direction: row-reverse;">اسم
                                        المخالفه</label>
                                    <input type="text" id="nameedit" name="nameedit" class="form-control"
                                        placeholder="اسم المخالفه" required>
                                </div>
                                <div class="form-group  mb-3">
                                    <label class="d-flex justify-content-start pb-2" for="types"
                                        style=" flex-direction: row-reverse;">
                                        الاداره الخاصه بالمخالفه</label>
                                    <select class="w-100 px-2" name="types[]" id="types" multiple
                                        style="border: 0.2px solid rgb(199, 196, 196);" required dir="rtl">
                                        @foreach (getDepartments() as $department)
                                            <option value="{{ $department->id }}"> {{ $department->name }}</option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2" dir="rtl">
                                    <button type="submit" class="btn-all mx-2 p-2"
                                        style="background-color: #274373; color: #ffffff;" id="openSecondModalBtn">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> اضافة
                                    </button>
                                    <button type="button" class="btn-all p-2"
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="secondModalBody" class="d-none">
                        <div class="body-img-modal d-block mb-4">
                            <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                            <p>تمت الاضافه بنجاح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection
@push('scripts')
    <script>
        function openedit(id, name, types) {
            document.getElementById('nameedit').value = name;
            document.getElementById('idedit').value = id;

            // Ensure types is an array
            if (typeof types === 'string') {
                try {
                    types = JSON.parse(types);
                } catch (e) {
                    console.error('Error parsing types:', e);
                    types = [];
                }
            }

            console.log('Types:', types); // Debugging

            let select = document.getElementById('types');
            let options = select.options;

            // Clear previous selections
            for (let i = 0; i < options.length; i++) {
                options[i].selected = true;
            }
            console.log('options:', options);
            // Set new selections
            for (let i = 0; i < options.length; i++) {

                // let optionValue = parseInt(options[i].value);
                let optionValue = options[i].value;
                // console.log('options:', options);
                console.log('Option value (parsed):', optionValue); // Debugging

                if (types.includes(optionValue)) {
                    options[i].selected = true;
                    options[i].setAttribute('selected', 'selected');
                    options[i].classList.add('selected-option');
                    console.log('Option selected:', options[i]); // Debugging
                }
            }

            $('#edit').modal('show');
        }

        function confirmEdit() {
            var id = document.getElementById('id').value;
            document.getElementById('types').value = types;

            var form = document.getElementById('edit-form');

            // form.submit();
        }

        function openadd() {
            $('#add').modal('show');
        }


        $(document).ready(function() {


            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('violations.getAllviolations') }}', // Correct URL concatenation
                columns: [{
                        data: 'name',
                        sWidth: '50px',
                        name: 'name'
                    },
                    {
                        data: 'type_name',
                        sWidth: '50px',
                        name: 'type_name'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        sWidth: '100px',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                "oLanguage": {
                    "sSearch": "",
                    "sSearchPlaceholder": "بحث",
                    "sInfo": 'اظهار صفحة _PAGE_ من _PAGES_',
                    "sInfoEmpty": 'لا توجد بيانات متاحه',
                    "sInfoFiltered": '(تم تصفية  من _MAX_ اجمالى البيانات)',
                    "sLengthMenu": 'اظهار _MENU_ عنصر لكل صفحة',
                    "sZeroRecords": 'نأسف لا توجد نتيجة',
                    "oPaginate": {
                        "sFirst": '<i class="fa fa-fast-backward" aria-hidden="true"></i>', // This is the link to the first page
                        "sPrevious": '<i class="fa fa-chevron-left" aria-hidden="true"></i>', // This is the link to the previous page
                        "sNext": '<i class="fa fa-chevron-right" aria-hidden="true"></i>', // This is the link to the next page
                        "sLast": '<i class="fa fa-step-forward" aria-hidden="true"></i>' // This is the link to the last page
                    }


                },
                layout: {
                    bottomEnd: {
                        paging: {
                            firstLast: false
                        }
                    }
                },
                "pagingType": "full_numbers"

            });


        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectBox = document.getElementById('select-box');
            const options = document.getElementById('options');
            const searchInput = document.getElementById('search-input');
            const selectedValuesContainer = document.getElementById('selected-values');
            const optionCheckboxes = document.querySelectorAll('.option input[type="checkbox"]');
            selectBox.addEventListener('click', function() {
                options.style.display = options.style.display === 'block' ? 'none' : 'block';
            });
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.select-wrapper')) {
                    options.style.display = 'none';
                }
            });
            optionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const selectedOptions = Array.from(optionCheckboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.nextElementSibling.textContent);
                    selectedValuesContainer.innerHTML = selectedOptions.join(', ');
                });
            });
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                optionCheckboxes.forEach(checkbox => {
                    const optionLabel = checkbox.nextElementSibling.textContent.toLowerCase();
                    checkbox.parentElement.style.display = optionLabel.includes(searchTerm) ?
                        'block' : 'none';
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('add-form').addEventListener('submit', function(event) {
                event.preventDefault();

                var name = document.getElementById('nameadd').value;
                var checkboxes = document.querySelectorAll('input[name="types[]"]');
                var typesSelected = Array.from(checkboxes).some(checkbox => checkbox.checked);

                var form = document.getElementById('add-form');
                var inputs = form.querySelectorAll('[required]');
                var valid = true;

                // Clear previous error messages and styles
                inputs.forEach(function(input) {
                    input.style.borderColor = ''; // Reset border color
                });
                document.getElementById('select-box').style.borderColor = ''; // Reset border color

                // Validate required inputs
                inputs.forEach(function(input) {
                    if (!input.value) {
                        valid = false;
                        input.style.borderColor = 'red'; // Highlight empty inputs
                        alert('من فضلك ادخل اسم نوع الشكوى');
                    }
                });

                // Validate checkboxes
                if (!typesSelected) {
                    valid = false;
                    document.getElementById('select-box').style.borderColor =
                        'red'; // Highlight empty inputs
                    alert('من فضلك اختر الأداره الخاصه بالشكوى');
                }

                // If all validations pass, submit the form
                if (valid) {
                    this.submit();
                }
            });
        });
    </script>
@endpush
