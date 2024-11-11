@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>

@section('content')
@section('title')
    مسميات العجز
@endsection
<section>
    <div class="row">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p>
                    مسميات العجز
                </p>
                <button type="button" class="wide-btn" data-bs-toggle="modal" data-bs-target="#myModal1"
                    style="    color: #0D992C;">
                    اضافة نوع جديدة <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="container  col-11 mt-3 p-0  pt-5 pb-4">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('reject'))
                <div class="alert alert-danger">
                    {{ session('reject') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="col-lg-12">
                <div class="bg-white ">
                    <div>
                        <table id="users-table"
                            class="display table table-responsive-sm  table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>رقم التعريف</th>
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

    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center ">
                        <h5 class="modal-title"> اضافة </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body" dir="rtl">
                    <form id="createForm" action="{{ route('absence.store') }}" method="post">
                        @csrf
                        <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                            <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                <div class="form-group mt-4 mb-3">
                                    <label class="d-flex justify-content-start pt-3 pb-2" for="name">
                                        الاسم </label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        placeholder="الاسم " required>
                                </div>
                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                    <button type="submit" class="btn-all mx-2 "
                                        style="background-color: #274373; color: #ffffff;"
                                        id="openSecondModalBtncreate">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> اضافة
                                    </button>
                                    <button type="button" class="btn-all "
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- Second Modal Body (Initially Hidden) -->
                    <div id="secondModalBodycreate" class="d-none">
                        <div class="body-img-modal d-block mb-4">
                            <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                            <p>تمت الاضافه بنجاح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form Modal -->
    <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center ">
                        <h5 class="modal-title">تعديل </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body" dir="rtl">
                    <form id="editForm" action="{{ route('absence_update') }}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="id_edit" name="id_edit">
                        <div id="firstModalBody1" class="mb-3 mt-3 d-flex justify-content-center">
                            <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                <div class="form-group mt-4 mb-3">
                                    <label class="d-flex justify-content-start pt-3 pb-2" for="name_edit">الاسم
                                    </label>
                                    <input type="text" id="name_edit" name="name_edit" class="form-control"
                                        placeholder="الاسم" required>
                                </div>

                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                    <button type="submit" class="btn-all mx-2 "
                                        style="background-color: #274373; color: #ffffff;" id="openSecondModalBtn1">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> تعديل
                                    </button>
                                    <button type="button" class="btn-all "
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- Second Modal Body (Initially Hidden) -->
                    <div id="secondModalBody1" class="d-none">
                        <div class="body-img-modal d-block mb-4">
                            <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                            <p>تمت التعديل بنجاح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="opendelete" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="opendelete"> !تنبــــــيه</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body  mt-3 mb-5">
                    <div class="container pt-5 pb-3" style="border: 0.2px solid rgb(166, 165, 165);">
                        <form id="delete-form" action="{{ route('absence.delete') }}" method="POST">
                            @csrf
                            <div class="form-group d-flex justify-content-center ">
                                <h5 class="modal-title " id="opendelete"> هل تريد حذف هذه المسميات ؟</h5>


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

</section>

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
    $(document).ready(function() {
        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url('api/absence') }}',
            columns: [{
                    data: 'id',
                    sWidth: '50px',
                    name: 'id'
                },
                {
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
            /*columnDefs: [{
                targets: -1,
                render: function(data, type, row) {
                    return `
                        <a href="#" class="btn btn-sm" style="background-color: #F7AF15;" onclick="openEditModal('${row.id}', '${row.name}')"> <i class="fa fa-edit"></i> تعديل </a>

                     `;
                }

            }],*/
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
<script>
    function openEditModal(id, name) {


        $.ajax({
            url: '/absence/edit/' + id,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    // Populate modal fields with data
                    document.getElementById('name_edit').value = data.name;
                    document.getElementById('id_edit').value = data.id;
                    $('#edit').modal('show');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error retrieving data');
            }
        });


        // console.log('id', id);

        // $obj = WorkingTime::find(id);
        // // document.getElementById('nameedit').value = name;
        // // document.getElementById('idedit').value = id;
        // $('#edit').modal('show');
    }

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
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editForm = document.getElementById('editForm');
        var openSecondModalBtn1 = document.getElementById('openSecondModalBtn1');
        var firstModalBody1 = document.getElementById('firstModalBody1');
        var secondModalBody1 = document.getElementById('secondModalBody1');

        editForm.addEventListener('submit', function(event) {
            event.preventDefault();
            console.log('Form submitted');

            var isValid1 = editForm.checkValidity();
            if (isValid1) {
                console.log('Form is valid');

                // Perform AJAX form submission
                $.ajax({
                    type: editForm.method,
                    url: editForm.action,
                    data: $(editForm).serialize(),
                    success: function(response) {
                        console.log('AJAX success');
                        // Handle the response
                        // Optionally, reload the page
                        firstModalBody1.classList.add('d-none');
                        secondModalBody1.classList.remove('d-none');
                        window.location.reload();

                    },
                    error: function(error) {
                        console.error('AJAX error', error);
                    }
                });

            } else {
                // Show validation errors
                editForm.reportValidity();
            }
        });


    });
</script>
@endsection
