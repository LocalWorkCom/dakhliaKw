@extends('layout.main')
@push('style')
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
    <script type="text/javascript" charset="utf8"
        src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
    </script>
@endpush
@section('title')
    المناطق
@endsection
@section('content')
    <section>
        <div class="row">

            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> المنـــاطق</p>
                    {{-- @if (Auth::user()->hasPermission('create Region')) --}}
                    <button type="button" class="btn-all  " onclick="openadd()"
                        style="color: #0D992C;">
                        اضافة منطقة جديدة <img
                            src="{{ asset('frontend/images/add-btn.svg') }}"
                            alt="img">
                    </button>
                    {{-- @endif --}}
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">

                <div class="row " dir="rtl">
                    <div class="form-group mt-4  mx-md-2 col-12 d-flex ">
                        <!-- {{-- @if (Auth::user()->hasPermission('create Region')) --}}

                                                    <button type="button" class="btn-all  "
                                                    onclick="openadd()" style="color: #0D992C;">

                                                        اضافة جديد  <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                                                    </button>
                                                    {{-- @endif --}} -->

                        <div class="form-group moftsh  mx-3  d-flex">
                            <h4 style="margin-left:10px;line-height: 1.8;"> تصفية حسب </h4>
                            <select name="government-select" id="government-select" onchange="filterRegions()"
                                class=" form-group mx-md-2 btn-all  custom-select custom-select-lg mb-3 select2   "
                                style="text-align: center; color:#ff8f00;height: 40px;font-size: 19px; padding-inline:10px;">
                                <option value="" selected disabled> المحافظه</option>
                                @foreach (getgovernments() as $government)
                                    <option value="{{ $government->id }}"
                                        @if ($government->id == $id) selected @endif>
                                        {{ $government->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                {{-- <script>
                    $('#government-select').click(
                        $('#governorate').trigger('change');
                    );
                </script> --}}
                <div class="col-lg-12">
                    <div class="bg-white">
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
                                        <th>المحافظه التابعه لها</th>
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
    <div class="modal fade" id="add" tabindex="-1"
        aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="lable"> أضافه منطقه جديد</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container pt-4 pb-4" style="border: 0.2px solid rgb(166, 165, 165);">
                        <form class="edit-grade-form" id="add-form" action="{{ route('regions.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="nameadd">الاسم</label>
                                <input type="text" id="nameadd" name="nameadd" class="form-control" required>
                                <span class="text-danger span-error" id="nameadd-error" dir="rtl"></span>
                            </div>

                            <div class="form-group">
                                <label for="governmentid">المحافظات </label>
                                <select name="governmentid" id="governmentid"
                                    class="form-group col-md-12 custom-select custom-select-lg mb-3 select2"
                                    style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;" required>
                                    <option value="">اختر المحافظه</option>
                                    @foreach (getgovernments() as $government)
                                        <option value="{{ $government->id }}">
                                            {{ $government->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger span-error" id="governmentid-error" dir="rtl"></span>
                            </div>

                            <!-- Save button -->
                            <div class="text-end">
                                <button type="submit" class="btn-blue" onclick="confirmAdd(event)">اضافه</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- this for edit form --}}
    <div class="modal fade" id="edit" tabindex="-1"
        aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="label">تعديل اسم المنطقه ؟</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container pt-4 pb-4" style="border: 0.2px solid rgb(166, 165, 165);">
                        <form class="edit-grade-form" id="edit-form" action="{{ route('regions.update') }}"
                            method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="name">الاسم</label>
                                <input type="text" id="nameedit" name="name" class="form-control" dir="rtl"
                                    required>
                                <span class="text-danger span-error" id="nameedit-error" dir="rtl"></span>
                                <!-- Error message for name -->
                            </div>

                            <input type="hidden" id="idedit" name="id" value="">
                            <!-- Hidden field for ID -->

                            <div class="form-group">
                                <label for="government">المحافظات</label>
                                <select name="government" id="government" class="form-group col-md-12" required>
                                    <option value="">اختر المحافظه</option>
                                    @foreach (getgovernments() as $government)
                                        <option value="{{ $government->id }}">
                                            {{ $government->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger span-error" id="governmentidedit-error" dir="rtl"></span>
                                <!-- Error message for government -->
                            </div>

                            <!-- Save button -->
                            <div class="text-end">
                                <button type="submit" class="btn-blue" onclick="confirmEdit(event)">تعديل</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- model for delete form --}}
    {{-- <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="deleteModalLabel"> !تنبــــــيه</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                        </button>
                    </div>
                </div>
                <form id="delete-form" action="{{ route('regions.delete') }}" method="POST">
                    @csrf
                    <div class="modal-body  d-flex justify-content-center">
                        <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف هذه الرتبه ؟</h5>


                        <input type="text" id="id" hidden name="id" class="form-control">
                    </div>
                    <div class="modal-footer mx-2 d-flex justify-content-center">
                        <div class="text-end">
                            <button type="button" class="btn-blue">لا</button>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn-blue" onclick="confirmDelete()">نعم</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
@endsection
@push('scripts')
    <script
        src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js">
    </script>

    <script>
        $('.select2').select2({
            dir: "rtl"
        });

        function openedit(id, name, government) {
            document.getElementById('nameedit').value = name;
            document.getElementById('government').value = government;
            document.getElementById('idedit').value = id;

            $('#edit').modal('show');


        }

        function confirmEdit(event) {
            event.preventDefault(); // Prevent default form submission

            // Get input fields and trim any leading/trailing spaces
            var name = document.getElementById('nameedit').value.trim();
            var governmentId = document.getElementById('government').value; // Get the selected government ID

            // Clear previous errors
            document.getElementById('nameedit-error').textContent = '';
            document.getElementById('governmentidedit-error').textContent = ''; // Clear government error

            var hasError = false;

            // Check if the name is empty or consists only of spaces
            if (name === '' || name.length === 0) {
                document.getElementById('nameedit-error').textContent = 'الاسم مطلوب ولا يمكن أن يحتوي على مسافات فقط.';
                hasError = true;
            }

            // Check if the government select is not selected
            if (governmentId === '') {
                document.getElementById('governmentidedit-error').textContent = 'المحافظه مطلوبه.';
                hasError = true;
            }

            // If no errors, submit the form
            if (!hasError) {
                document.getElementById('edit-form').submit(); // Submit the form
            }
        }



        function openadd() {

            $('#add').modal('show');
            $('#exampleModal').on('shown.bs.modal', function() {
                $('#government-select').select2({
                    width: 'resolve',
                    placeholder: 'اختر المحافظه',
                    allowClear: true
                });
            });
        }
        //    var government = document.getElementById('governmentid').value;


        function confirmAdd(event) {
            event.preventDefault(); // Prevent default form submission

            // Get input fields and trim any leading/trailing spaces
            var name = document.getElementById('nameadd').value.trim();
            var governmentId = document.getElementById('governmentid').value; // Get the selected government ID

            // Clear previous errors
            document.getElementById('nameadd-error').textContent = '';
            document.getElementById('governmentid-error').textContent = ''; // Clear government error

            var hasError = false;

            // Check if the name is empty or consists only of spaces
            if (name === '' || name.length === 0) {
                document.getElementById('nameadd-error').textContent = 'الاسم مطلوب ولا يمكن أن يحتوي على مسافات فقط.';
                hasError = true;
            }

            // Check if the government select is not selected
            if (governmentId === '') {
                document.getElementById('governmentid-error').textContent = 'المحافظه مطلوبه.';
                hasError = true;
            }

            // If no errors, submit the form
            if (!hasError) {
                document.getElementById('add-form').submit(); // Submit the form
            }
        }

        var table;

        $(document).ready(function() {
            $.fn.dataTable.ext.classes.sPageButton =
            'btn-pagination btn-sm'; // Change Pagination Button Class

            table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                bResetDisplay: true,
                ajax: {
                    url: '{{ route('getAllregions') }}',
                    data: function(d) {
                        d.government_id = $(
                            '#government-select')
                    .val(); // Add government_id to request
                    }
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'government_name',
                        name: 'government_name'
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
                        "sFirst": '<i class="fa fa-fast-backward" aria-hidden="true"></i>',
                        "sPrevious": '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                        "sNext": '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
                        "sLast": '<i class="fa fa-step-forward" aria-hidden="true"></i>'
                    }
                },
                "pagingType": "full_numbers",
                "fnDrawCallback": function(oSettings) {
                    console.log('Page ' + this.api().page.info().pages)
                    var page = this.api().page.info().pages;
                    console.log($('#users-table tr').length);
                    if (page == 1) {
                        //   $('.dataTables_paginate').hide();//css('visiblity','hidden');
                        $('.dataTables_paginate').css('visibility', 'hidden'); // to hide

                    }
                }
            });

            $(".dataTables_filter input")
                .unbind() // Unbind previous default bindings
                .bind("input", function(e) { // Bind our desired behavior
                    // If the length is 3 or more characters, or the user pressed ENTER, search
                    if (this.value.length >= 3 || e.keyCode == 13) {
                        // Call the API search function
                        dtable.search(this.value).draw();
                    }
                    // Ensure we clear the search if they backspace far enough
                    if (this.value == "") {
                        dtable.search("").draw();
                    }
                    return;
                });
            $('#government-select').change(function() {
                table.ajax
            .reload(); // Reload DataTable data on dropdown change
            });
        });

        function filterRegions() {
            if (table) {
                table.ajax.reload(); // Reload DataTable with new filter
            }
        }
    </script>
@endpush
