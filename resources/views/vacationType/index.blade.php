@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title')
    أنواع الأجازات
@endsection
@section('content')
    <section>
        <div class="row">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> أنواع الاجـــــازات</p>
                    @if (Auth::user()->hasPermission('create VacationType'))
                        <button type="button" class="btn-all  " onclick="openadd()" style="color: #0D992C;">
                            اضافة جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">

                        </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">

        </div>

        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">

                <div class="row " dir="rtl">
                    <div class="form-group mt-4  mx-md-2 col-12 d-flex ">

                    </div>
                </div>
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
                                        <th>الظهور في التطبيق</th>
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
    <div class="modal fade" id="add" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="lable"> أضافه نوع أجازه جديد</h5>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body  mt-3 mb-5 ">
                    <div class="container pt-5 pb-3" style="border: 0.2px solid rgb(166, 165, 165);">
                        <form class="edit-grade-form" id="add-form" action=" {{ route('vacationType.add') }}"
                            method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">الاسم</label>
                                <input type="text" id="nameadd" name="nameadd" class="form-control" required>
                                <span class="text-danger span-error" id="Civil_number-error" dir="rtl"></span>

                            </div>
                            <div class="form row mx-md-3 mt-4 d-flex justify-content-center">
                                <div class="form-group col-md-10 mx-md-md-2  d-flex" dir="rtl">
                                    <input type="checkbox" class="mx-2" name="flag" id="toggleCheckbox" value="1">
                                    <label for="toggleCheckbox"> الظهور ف التطبيق</label>
                                </div>
                            </div>
                            <!-- Save button -->
                            <div class="text-end">
                                <button type="submit" class="btn-blue" onclick="confirmAdd()">اضافه</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- this for edit form --}}

    <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="lable"> تعديل اسم الأجازه ؟</h5>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body  mt-3 mb-5">
                    <div class="container pt-5 pb-3" style="border: 0.2px solid rgb(166, 165, 165);">
                        <form class="edit-grade-form" id="edit-form" action=" {{ route('vacationType.update') }}"
                            method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">الاسم</label>
                                <input type="text" id="nameedit" value="" name="name" class="form-control"
                                    required dir="rtl">
                                <div class="form row mx-md-3 mt-4 d-flex justify-content-center">
                                    <div class="form-group col-md-10 mx-md-md-2  d-flex" dir="rtl">
                                        <input type="checkbox" class="mx-2" name="flag" id="toggleCheckbox_edit"
                                            value="1">
                                        <label for="toggleCheckbox"> الظهور ف التطبيق</label>
                                    </div>
                                </div>
                                <input type="text" id="idedit" value="" name="id" hidden
                                    class="form-control" dir="rtl">

                            </div>
                            <!-- Save button -->
                            <div class="text-end">
                                <button type="submit" class="btn-blue" onclick="confirmEdit()">تعديل</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- model for delete form --}}
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="deleteModalLabel"> !تنبــــــيه</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body  mt-3 mb-5">
                    <div class="container pt-5 pb-3" style="border: 0.2px solid rgb(166, 165, 165);">
                        <form id="delete-form" action="{{ route('vacationType.delete') }}" method="POST">
                            @csrf
                            <div class="form-group d-flex justify-content-center ">
                                <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف هذه الأجازه ؟</h5>


                                <input type="text" id="id" value="" hidden name="id"
                                    class="form-control">
                            </div>

                            <!-- Save button -->
                            <div class="text-end">
                                <div class="modal-footer mx-2 d-flex justify-content-center">
                                    <div class="text-end">
                                        <button type="button" class="btn-blue" id="closeButton">لا</button>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn-blue" onclick="confirmDelete()">نعم</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            function closeModal() {
                $('#delete').modal('hide');

            }

            $('#closeButton').on('click', function() {
                closeModal();
            });
        });
    </script>
    <script>
        function opendelete(id) {
            document.getElementById('id').value = id;
            $('#delete').modal('show');
        }

        function confirmDelete() {
            var id = document.getElementById('id').value;
            console.log(id);
            var form = document.getElementById('delete-form');

            form.submit();

        }

        function openedit(id, name) {
            document.getElementById('nameedit').value = name;
            document.getElementById('idedit').value = id;
            document.getElementById('toggleCheckbox_edit').checked = true;

            $('#edit').modal('show');


        }

        function confirmEdit() {
            var id = document.getElementById('id').value;
            var form = document.getElementById('edit-form');

            // form.submit();

        }

        function openadd() {
            $('#add').modal('show');
        }

        function confirmAdd() {
            var name = document.getElementById('nameadd').value;

            var form = document.getElementById('add-form');
            var inputs = form.querySelectorAll('[required]');
            var valid = true;

            inputs.forEach(function(input) {
                if (!input.value) {
                    valid = false;
                    input.style.borderColor = 'red'; // Optional: highlight empty inputs
                } else {
                    input.style.borderColor = ''; // Reset border color if input is filled
                }
            });

            if (valid) {
                form.submit();
            }
        }
        $(document).ready(function() {
            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('setting.getAllvacationType') }}', // Correct URL concatenation
                columns: [{
                        data: 'name',
                        sWidth: '50px',
                        name: 'name'
                    },
                    {
                        data: 'flag',
                        sWidth: '50px',
                        name: 'flag'
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
                "pagingType": "full_numbers",
                "fnDrawCallback": function(oSettings) {
                    var api = this.api();
                    var pageInfo = api.page.info();

                    // Check if the total number of records is less than or equal to the number of entries per page
                    if (pageInfo.recordsTotal <= 10) { // Adjust this number based on your page length
                        $('.dataTables_paginate').css('visibility', 'hidden'); // Hide pagination
                    } else {
                        $('.dataTables_paginate').css('visibility', 'visible'); // Show pagination
                    }
                }

            });


        });
    </script>
@endpush
