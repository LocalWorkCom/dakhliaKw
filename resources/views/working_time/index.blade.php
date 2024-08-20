@extends('layout.main')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('content')
@section('title')
    فترة العمل
@endsection

<div class="row ">
    <div class="container welcome col-11">
        <div class="d-flex justify-content-between">
            <p> فترة العمل</p>
            <button class="btn-all px-3" style="color: #274373;" data-bs-toggle="modal" data-bs-target="#myModal1">

                اضافة فترة <img src="{{ asset('frontend/images/time.svg') }}" alt="">
            </button>
        </div>

    </div>
</div>
<br>

<div class="row">
    <div class="container  col-11 mt-3 p-0  pt-5 pb-4">
        <!-- <div class="row d-flex justify-content-between " dir="rtl">
            <div class="form-group mt-4 mx-3  d-flex">
                <button class="btn-all px-3" style="color: #274373;" data-bs-toggle="modal" data-bs-target="#myModal1">
                    <img src="{{ asset('frontend/images/time.svg') }}" alt="">
                    اضافة فترة
                </button>
            </div>
        </div> -->

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
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
                                <th>رقم التسلسلي</th>
                                <th>اسم الفترة</th>
                                <th>بدايه وقت العمل </th>
                                <th> نهاية وقت العمل</th>
                                <th> لون الفترة</th>
                                <th style="width:150px !important;">العمليات</th>
                            </tr>
                        </thead>
                    </table>

                    <script>
                        $(document).ready(function() {
                            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm';

                            $('#users-table').DataTable({
                                processing: true,
                                serverSide: true,
                                ajax: '{{ url('api/working_time') }}',
                                bAutoWidth: false,
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
                                        data: 'start_time',
                                        name: 'start_time'
                                    },
                                    {
                                        data: 'end_time',
                                        name: 'end_time'
                                    },
                                    {
                                        data: 'color',
                                        name: 'color',
                                        render: function(data, type, row) {
                                            if (type === 'display') {
                                                if (data === null || data === '') {
                                                    return 'لا يوجد لون';
                                                } else {
                                                    return `<div style="width: 25px; height: 25px; border-radius:50%; background-color: ${data}; border: 1px solid #000;" class="d-flex justify-content-center align-items-center"></div>`;
                                                }
                                            }
                                            return data;
                                        }

                                    },
                                    {
                                        data: 'action',
                                        name: 'action',
                                        sWidth: '200px',
                                        orderable: false,
                                        searchable: false
                                    }
                                ],
                                columnDefs: [{
                                    targets: -1,
                                    render: function(data, type, row) {
                                        return `
                                            <a href="#" class="btn btn-sm " style="background-color: #274373;" onclick="openViewModal('${row.id}', '${row.name}')"> <i class="fa fa-eye"></i>عرض  </a>
                                            <a href="#" class="btn btn-sm" style="background-color: #F7AF15;" onclick="openEditModal('${row.id}', '${row.name}')"> <i class="fa fa-edit"></i> تعديل </a>
                                        `;
                                    }
                                }],
                                oLanguage: {
                                    sSearch: "",
                                    sSearchPlaceholder: "بحث",
                                    sInfo: 'اظهار صفحة _PAGE_ من _PAGES_',
                                    sInfoEmpty: 'لا توجد بيانات متاحه',
                                    sInfoFiltered: '(تم تصفية من _MAX_ اجمالى البيانات)',
                                    sLengthMenu: 'اظهار _MENU_ عنصر لكل صفحة',
                                    sZeroRecords: 'نأسف لا توجد نتيجة',
                                    oPaginate: {
                                        "sFirst": '<i class="fa fa-fast-backward" aria-hidden="true"></i>', // This is the link to the first page
                                        "sPrevious": '<i class="fa fa-chevron-left" aria-hidden="true"></i>', // This is the link to the previous page
                                        "sNext": '<i class="fa fa-chevron-right" aria-hidden="true"></i>', // This is the link to the next page
                                        "sLast": '<i class="fa fa-step-forward" aria-hidden="true"></i>' // This is the link to the last page
                                    }
                                },

                                pagingType: "full_numbers"
                            });
                        });
                    </script>
                </div>

                <!-- Create Form Modal -->
                <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-center">
                                <div class="title d-flex flex-row align-items-center ">
                                    <h5 class="modal-title"> اضافة فترة </h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    &times;
                                </button>
                            </div>
                            <div class="modal-body" dir="rtl">
                                <form id="createForm" action="{{ route('working_time.store') }}" method="post">
                                    @csrf
                                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                            <div class="form-group mt-4 mb-3">
                                                <label class="d-flex justify-content-start pt-3 pb-2" for="name">
                                                    اسم الفتره</label>
                                                <input type="text" id="name" name="name" class="form-control"
                                                    placeholder="اسم الفتره" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="d-flex justify-content-start pb-2" for="start_time"> بداية
                                                    فترة العمل</label>
                                                <input type="time" id="start_time" name="start_time"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="d-flex justify-content-start pb-2" for="end_time"> نهاية
                                                    فترة العمل</label>
                                                <input type="time" id="end_time" name="end_time"
                                                    class="form-control" required>
                                            </div>
                                            <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                                <button type="submit" class="btn-all mx-2 p-2"
                                                    style="background-color: #274373; color: #ffffff;"
                                                    id="openSecondModalBtncreate">
                                                    <img src="{{ asset('frontend/images/white-add.svg') }}"
                                                        alt="img"> اضافة
                                                </button>
                                                <button type="button" class="btn-all p-2"
                                                    style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                    <img src="{{ asset('frontend/images/red-close.svg') }}"
                                                        alt="img"> الغاء
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
                <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="representativeLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-center">
                                <div class="title d-flex flex-row align-items-center ">
                                    <h5 class="modal-title">تعديل فترة</h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close">&times;</button>
                            </div>
                            <div class="modal-body" dir="rtl">
                                <form id="editForm" action="{{ route('working_time.update') }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="id_edit" name="id_edit">
                                    <div id="firstModalBody1" class="mb-3 mt-3 d-flex justify-content-center">
                                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                            <div class="form-group mt-4 mb-3">
                                                <label class="d-flex justify-content-start pt-3 pb-2"
                                                    for="name_edit">اسم الفتره</label>
                                                <input type="text" id="name_edit" name="name_edit"
                                                    class="form-control" placeholder="اسم الفتره" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="d-flex justify-content-start pb-2"
                                                    for="start_time_edit">بداية فترة العمل</label>
                                                <input type="time" id="start_time_edit" name="start_time_edit"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="d-flex justify-content-start pb-2"
                                                    for="end_time_edit">نهاية فترة العمل</label>
                                                <input type="time" id="end_time_edit" name="end_time_edit"
                                                    class="form-control" required>
                                            </div>
                                            <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                                <button type="submit" class="btn-all mx-2 p-2"
                                                    style="background-color: #274373; color: #ffffff;"
                                                    id="openSecondModalBtn1">
                                                    <img src="{{ asset('frontend/images/white-add.svg') }}"
                                                        alt="img"> تعديل
                                                </button>
                                                <button type="button" class="btn-all p-2"
                                                    style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                    <img src="{{ asset('frontend/images/red-close.svg') }}"
                                                        alt="img"> الغاء
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

                <!-- view Form Modal -->
                <div class="modal fade" id="view" tabindex="-1" aria-labelledby="representativeLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" dir="rtl">
                            <div class="modal-header d-flex justify-content-center">
                                <div class="title d-flex flex-row align-items-center ">
                                    <h5 class="modal-title"> عرض فترة </h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    &times;
                                </button>
                            </div>
                            <div class="modal-body">

                                <div id="" class="mb-3 mt-3 d-flex justify-content-center">
                                    <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                        <div class="form-group mt-4 mb-3">
                                            <label class="d-flex justify-content-start pt-3 pb-2" for="name_show">
                                                اسم الفتره</label>
                                            <input type="text" id="name_show" name="name_show"
                                                class="form-control" placeholder="اسم الفتره" disabled>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="d-flex justify-content-start pb-2" for="start_time_show">
                                                بداية فترة العمل</label>
                                            <input type="time" id="start_time_show" name="start_time_show"
                                                class="form-control" disabled>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="d-flex justify-content-start pb-2" for="end_time_show">
                                                نهاية
                                                فترة العمل</label>
                                            <input type="time" id="end_time_show" name="end_time_show"
                                                class="form-control" disabled>
                                        </div>

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function openViewModal(id, name) {
                        $.ajax({
                            url: '/working_time/show/' + id,
                            method: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    var data = response.data;
                                    // Populate modal fields with data
                                    document.getElementById('name_show').value = data.name;
                                    document.getElementById('start_time_show').value = data.start_time;
                                    document.getElementById('end_time_show').value = data.end_time;
                                    // document.getElementById('id_show').value = data.id;
                                    $('#view').modal('show');
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function() {
                                alert('Error retrieving data');
                            }
                        });
                    }

                    function openEditModal(id, name) {


                        $.ajax({
                            url: '/working_time/edit/' + id,
                            method: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    var data = response.data;
                                    // Populate modal fields with data
                                    document.getElementById('name_edit').value = data.name;
                                    document.getElementById('start_time_edit').value = data.start_time;
                                    document.getElementById('end_time_edit').value = data.end_time;
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

                    document.addEventListener('DOMContentLoaded', function() {
                        var createForm = document.getElementById('createForm');
                        var openSecondModalBtn = document.getElementById('openSecondModalBtncreate');
                        var firstModalBody = document.getElementById('firstModalBody');
                        var secondModalBodycreate = document.getElementById('secondModalBodycreate');

                        createForm.addEventListener('submit', function(event) {
                            event.preventDefault();
                            console.log('Form submitted');

                            var isValid1 = createForm.checkValidity();
                            if (isValid1) {
                                console.log('Form is valid');

                                // Perform AJAX form submission
                                $.ajax({
                                    type: createForm.method,
                                    url: createForm.action,
                                    data: $(createForm).serialize(),
                                    success: function(response) {
                                        console.log('AJAX success');
                                        // Handle the response
                                        // Optionally, reload the page
                                        firstModalBody.classList.add('d-none');
                                        secondModalBodycreate.classList.remove('d-none');
                                        window.location.reload();

                                    },
                                    error: function(error) {
                                        console.error('AJAX error', error);
                                    }
                                });

                                // Switch modal content on successful validation
                                firstModalBody.classList.add('d-none');
                                secondModalBodycreate.classList.remove('d-none');
                            } else {
                                // Show validation errors
                                createForm.reportValidity();
                            }
                        });

                        // openSecondModalBtn.addEventListener('click', function(event) {
                        //     event.preventDefault();
                        //     console.log('Second modal button clicked');

                        //     var isValid = createForm.checkValidity();
                        //     if (isValid) {
                        //         console.log('Form is valid for second modal');

                        //         // Switch modal content on successful validation
                        //         firstModalBody.classList.add('d-none');
                        //         secondModalBodycreate.classList.remove('d-none');
                        //     } else {
                        //         // Show validation errors
                        //         createForm.reportValidity();
                        //     }
                        // });
                    });
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

                                // Switch modal content on successful validation
                                // firstModalBody1.classList.add('d-none');
                                // secondModalBody1.classList.remove('d-none');
                            } else {
                                // Show validation errors
                                editForm.reportValidity();
                            }
                        });

                        // openSecondModalBtn1.addEventListener('click', function(event) {
                        //     event.preventDefault();
                        //     console.log('Second modal button clicked');

                        //     var isValid = editForm.checkValidity();
                        //     if (isValid) {
                        //         console.log('Form is valid for second modal');

                        //         // Switch modal content on successful validation
                        //         firstModalBody1.classList.add('d-none');
                        //         secondModalBody1.classList.remove('d-none');
                        //     } else {
                        //         // Show validation errors
                        //         editForm.reportValidity();
                        //     }
                        // });
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
