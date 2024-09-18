@extends('layout.main')
@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
    </script>
@endpush
@section('title')
    الاعدادات
@endsection
@section('content')
    <div class="row">

        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p> الاعدادات</p>
                {{-- @if (Auth::user()->hasPermission('create Sector')) --}}
                <button type="button" class="btn-all" data-bs-toggle="modal" data-bs-target="#myModal1"
                    style="color: #0D992C;">

                    اضافة اعداد جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                </button>
                {{-- @endif --}}
            </div>
        </div>
    </div>

    <br>
    <div class="row">

        <div class="container  col-11 mt-3 p-0  pt-5 pb-4">
            <div class="row " dir="rtl">

            </div>
            <div class="col-lg-12">
                <div class="bg-white">
                    @if (session()->has('success'))
                        <div class="alert alert-info">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div>
                        <table id="users-table"
                            class="display table table-responsive-sm  table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>القيمة</th>
                                    <th style="width:150px;">العمليات</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <script>
        @if (session('showModal'))
            $(document).ready(function() {
                $('#myModal1').modal('show');

            });
        @endif
        @if (session('editModal'))
            $(document).ready(function() {
                $('#edit').modal('show');
            });
        @endif
        function deleteSetting(id) {
            $.ajax({
                url: "{{ route('setting.delete') }}",
                data: {
                    id: id
                },
                method: 'GET',
                success: function(response) {

                    window.location.reload();
                    alert('Deleted sucessfully');

                },
                error: function() {
                    alert('Error retrieving data');
                }
            });

        }

        function openViewModal(id, name) {
            // console.log("id", id);
            $.ajax({
                url: '/groups/show/' + id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        // console.log("data",data);

                        // // Populate modal fields with data
                        document.getElementById('nameadd_show').value = data.group.name;
                        document.getElementById('points_inspector_show').value = data.group.points_inspector;
                        document.getElementById('sector_show_id').value = data.group.sector_id;
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

        function openEditModal(id, key, value) {
            $('#edit').modal('show');
            document.getElementById('key_edit').value = key;
            document.getElementById('value_edit').value = value;
            document.getElementById('id_edit').value = id;
        }

        function SaveEdit(params) {

        }
        $.ajax({
            url: '/setting/update',
            data: {
                id: id
            },
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    var data = response.data;

                    // Populate modal fields with data
                    document.getElementById('key_edit').value = data.key;
                    document.getElementById('value_edit').value = data.value;
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
    </script>
    <div class="modal fade" id="myModal1" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="padding-left: 0px;" dir="rtl">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">
                        <h5 class="modal-title"> اضافة اعداد</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <form id="add-form" action="{{ route('setting.store') }}" method="POST">
                                @csrf
                                <div class="form-group mt-4 mb-3">
                                    <label for="keyadd" class="d-flex justify-content-start pt-3 pb-2">ادخل الاسم
                                    </label>
                                    <input type="text" id="keyadd" name="key" class="form-control"
                                        placeholder=" اكتب " value="{{ old('key') }}">
                                    @if ($errors->has('key'))
                                        <span class="text-danger">{{ $errors->first('key') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mt-4 mb-3">
                                    <label for="valueadd" class="d-flex justify-content-start pt-3 pb-2">ادخل القيمة
                                    </label>
                                    <input type="text" id="valueadd" name="value" class="form-control"
                                        placeholder=" اكتب " value="{{ old('value') }}">
                                    @if ($errors->has('value'))
                                        <span class="text-danger">{{ $errors->first('value') }}</span>
                                    @endif
                                </div>



                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                    <button type="submit" class="btn-all mx-2 "
                                        style="background-color: #274373; color: #ffffff;">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> اضافة
                                    </button>
                                    <button type="button" class="btn-all "
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                                {{-- @if (session('success'))
                                <div class="alert alert-success mt-2">
                                    {{ session('success') }}
                                </div>
                            @endif --}}


                            </form>
                        </div>
                    </div>
                    <!-- Second Modal Body (Initially Hidden) -->
                    <div id="secondModalBody" class="d-none">
                        <div class="body-img-modal d-block mb-4">
                            <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                            <p>تمت الاضافه بنجاح</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Second Modal Body (Initially Hidden) -->
            <div id="secondModalBody" class="d-none">
                <div class="body-img-modal d-block mb-4">
                    <img src="../images/ordered.svg" alt="">
                    <p>تمت الاضافه بنجاح</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="padding-left: 0px;" dir="rtl">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">
                        <h5 class="modal-title"> تعديل مجموعة</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <form id="add-form" action="{{ route('setting.update') }}" method="POST">
                                @csrf
                                <input type="hidden" id="id_edit" name="id_edit">
                                <div class="form-group mt-4 mb-3">
                                    <label for="key_edit" class="d-flex justify-content-start pt-3 pb-2">ادخل الاسم
                                    </label>
                                    <input type="text" id="key_edit" name="key" class="form-control"
                                        placeholder=" اكتب " value="{{ old('key') }}">
                                    @if ($errors->has('key'))
                                        <span class="text-danger">{{ $errors->first('key') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mt-4 mb-3">
                                    <label for="value_edit" class="d-flex justify-content-start pt-3 pb-2">ادخل القيمة
                                    </label>
                                    <input type="text" id="value_edit" name="value" class="form-control"
                                        placeholder=" اكتب " value="{{ old('value') }}">
                                    @if ($errors->has('value'))
                                        <span class="text-danger">{{ $errors->first('value') }}</span>
                                    @endif
                                </div>


                                <span class="text-danger span-error">
                                    @if ($errors->has('nothing_updated'))
                                        {{ $errors->first('nothing_updated') }}
                                    @endif
                                </span>

                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                    <button type="submit" class="btn-all mx-2 "
                                        style="background-color: #274373; color: #ffffff;">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> تعديل
                                    </button>
                                    <button type="button" class="btn-all "
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- view Form Modal -->
    <div class="modal fade" id="view" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true"
        style="padding-left: 0px;" dir="rtl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center ">
                        <h5 class="modal-title"> عرض الاعداد </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">

                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <div class="form-group mt-4 mb-3">
                                <label class="d-flex justify-content-start pt-3 pb-2" for="nameadd_show">
                                    اسم الاعداد </label>
                                <input type="text" id="nameadd_show" name="nameadd_show" class="form-control"
                                    placeholder="اكتب الاسم" disabled>
                            </div>

                            <div class="form-group mt-4 mb-3">
                                <label for="value_edit_show" class="d-flex justify-content-start pt-3 pb-2">ادخل القيمة
                                </label>
                                <input type="text" id="value_edit_show" name="value" class="form-control"
                                    placeholder=" اكتب القيمة " disabled>

                            </div>


                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm';
            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('get.settings') }}',
                }, // Correct URL concatenation
                columns: [{
                        data: 'key',
                        name: 'key'
                    },

                    {
                        data: 'value',
                        name: 'value'
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
                columnDefs: [{
                    targets: -1,
                    render: function(data, type, row) {
                        return `
                                                 <a href="#" class="btn btn-sm " style="background-color: #274373;" onclick="openViewModal('${row.id}', '${row.key}', '${row.value}')"> <i class="fa fa-eye"></i>عرض  </a>
                                                 <a href="#" class="btn btn-sm" style="background-color: #F7AF15;" onclick="openEditModal('${row.id}', '${row.key}','${row.value}')"> <i class="fa fa-edit"></i> تعديل </a>
                                                 <a href="#" class="btn btn-sm" style="background-color: red;" onclick="deleteSetting('${row.id}')"> <i class="fa fa-edit"></i> حذف </a>


                                                `;
                    }
                }],
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
