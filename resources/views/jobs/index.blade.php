@extends('layout.main')
@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
    </script>
@endpush

@section('title')
    المسمى الوظيفى
@endsection
@section('content')
    <section>
        <div class="row">

            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> المسمى الوظيفى</p>
                    @if (Auth::user()->hasPermission('edit job'))
                        <button type="button" class="btn-all  " onclick="openadd()" style="    color: #0D992C;">

                            اضافة وظيفة جديدة <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0  pt-5 pb-4">

                <!-- <div class="row " dir="rtl">
                        <div class="form-group mt-4  mx-md-2 col-12 d-flex ">
                            @if (Auth::user()->hasPermission('edit job'))
    <button type="button" class="btn-all  " onclick="openadd()" style="    color: #0D992C;">

                                اضافة وظيفة جديدة <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            </button>
    @endif
                        </div>
                    </div> -->
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
                        <h5 class="modal-title" id="lable"> أضافه مسمى وظيفى جديد</h5>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body mt-3 mb-5">
                    <div class="container pt-5 pb-4" style="border: 0.2px solid rgb(166, 165, 165);">
                        <form class="edit-grade-form" id="add-form" action=" {{ route('job.add') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">الاسم</label>
                                <input type="text" id="nameadd" name="nameadd" class="form-control" required>
                                <span class="text-danger span-error" id="nameadd-error" dir="rtl"></span>

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
    <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="label">تعديل على مسمى الوظيفه ؟</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body mt-3 mb-5">
                    <div class="container pt-5 pb-4" style="border: 0.2px solid rgb(166, 165, 165);">
                        <form class="edit-grade-form" id="edit-form" action="{{ route('job.update') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">الاسم</label>
                                <input type="text" id="nameedit" name="name" class="form-control" required>
                                <span class="text-danger span-error" id="nameedit-error" dir="rtl"></span> <!-- Error message for name -->
                                <input type="hidden" id="idedit" name="id" value=""> <!-- Hidden field for ID -->
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
                        <form id="delete-form" action="{{ route('job.delete') }}" method="POST">
                            @csrf
                            <div class="form-group d-flex justify-content-center ">
                                <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف هذا المسمى ؟</h5>


                                <input type="text" id="id" hidden name="id" class="form-control">
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
                <form id="delete-form" action="{{ route('job.delete') }}" method="POST">
                    @csrf
                    <div class="modal-body  d-flex justify-content-center mt-5 mb-5">
                        <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف هذه الوظيفه ؟</h5>


                        <input type="text" id="id" hidden name="id" class="form-control">
                    </div>
                    <div class="modal-footer mx-2 d-flex justify-content-center">
                        <div class="text-end">
                            <button type="button" class="btn-blue" id="closeButton">لا</button>
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
            var form = document.getElementById('delete-form');

            form.submit();

        }

        function openedit(id, name) {
            document.getElementById('nameedit').value = name;
            document.getElementById('idedit').value = id;

            $('#edit').modal('show');


        }

        function confirmEdit(event) {
    event.preventDefault(); // Prevent default form submission

    // Get the input fields
    var name = document.getElementById('nameedit').value.trim();

    // Clear previous error messages
    document.getElementById('nameedit-error').textContent = '';

    var hasError = false;

    // Check if the name is empty
    if (name === '') {
        document.getElementById('nameedit-error').textContent = 'الاسم مطلوب.';
        hasError = true;
    }

    // If no errors, submit the form
    if (!hasError) {
        document.getElementById('edit-form').submit(); // Submit the form
    }
}

        function openadd() {
            $('#add').modal('show');
        }

        function confirmAdd(event) {
            // var name = document.getElementById('nameadd').value;

            // var form = document.getElementById('add-form');
            // var inputs = form.querySelectorAll('[required]');
            // var valid = true;


            event.preventDefault(); // Prevent default form submission

            // Get input fields and trim any leading/trailing spaces
            var name = document.getElementById('nameadd').value.trim();

            // Clear previous errors
            document.getElementById('nameadd-error').textContent = '';

            var hasError = false;

            // Check if the name is empty or consists only of spaces
            if (name === '' || name.length === 0) {
                document.getElementById('nameadd-error').textContent = 'الاسم مطلوب ولا يمكن أن يحتوي على مسافات فقط.';
                hasError = true;
            }

            // If no errors, submit the form
            if (!hasError) {
                document.getElementById('add-form').submit(); // Submit the form
            }
            // if (valid) {
            //     form.submit();
            // }
        }
        $(document).ready(function() {
            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('setting.getAlljob') }}', // Correct URL concatenation
                columns: [{
                        data: 'name',
                        name: 'name'
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
                    console.log('Page ' + this.api().page.info().pages)
                    var page = this.api().page.info().pages;
                    console.log($('#users-table tr').length);
                    if (page == 1) {
                        //   $('.dataTables_paginate').hide();//css('visiblity','hidden');
                        $('.dataTables_paginate').css('visibility', 'hidden'); // to hide

                    }
                }
            });


        });
    </script>
@endpush
