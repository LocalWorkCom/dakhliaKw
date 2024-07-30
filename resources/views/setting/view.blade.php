@extends('layout.main')

@push('style')
@endpush
@section('title')
الاعدادات
@endsection
@section('content')
    <div class="container">

        <div class="row ">
            <div class="container welcome col-11">
                <p> الاعدادات</p>
            </div>
        </div>
        <div class="row " dir="rtl">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link @if ($activeTab == 1) active @endif" id="nav-home-tab"
                        data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab"
                        aria-controls="nav-home" aria-selected="true">رتب العسكريه</button>
                    <button class="nav-link @if ($activeTab == 2) active @endif" id="nav-profile-tab"
                        data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab"
                        aria-controls="nav-profile" aria-selected="false">الوظائف</button>
                    <button class="nav-link @if ($activeTab == 3) active @endif" id="nav-contact-tab"
                        data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab"
                        aria-controls="nav-contact" aria-selected="false">الاجازات</button>
                    <button class="nav-link @if ($activeTab == 4) active @endif" id="nav-contact-tab"
                        data-bs-toggle="tab" data-bs-target="#nav-gavernment" type="button" role="tab"
                        aria-controls="nav-gavernment" aria-selected="false">المحافظات</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade  @if ($activeTab == 1) show active @endif" id="nav-home"
                    role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="container  col-11 mt-3 p-0 ">
                        <div class="row justify-content-end">
                            <div class="col-auto">
                                <button type="button" class="wide-btn" data-bs-toggle="modal" id="extern-user-dev"
                                    data-bs-target="#add-grade" style="color: #0D992C;">
                                    <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                                    اضافةرتبه عسكريه
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row ">
                        <table id="grade-table" class="display table table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>الرقم</th>
                                    <th>الاسم</th>
                                    <th style="width:150px !important;">العمليات</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade @if ($activeTab == 2) show active @endif" id="nav-profile"
                    role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="container  col-11 mt-3 p-0 ">
                            <div class="row justify-content-end">
                                <div class="col-auto">
                                    <button type="button" class="wide-btn  " data-bs-toggle="modal" id="extern-user-dev"
                                        data-bs-target="#add-job" style="color: #0D992C;">
                                        <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                                        اضافة وظيفه
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row ">
                            <table id="job-table" class="display table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade @if ($activeTab == 3) show active @endif" id="nav-contact"
                    role="tabpanel" aria-labelledby="nav-contact-tab">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="container  col-11 mt-3 p-0 ">
                            <div class="row justify-content-end">
                                <div class="col-auto">
                                    <button type="button" class="wide-btn  " data-bs-toggle="modal" id="extern-user-dev"
                                        data-bs-target="#add-type" style="color: #0D992C;">
                                        <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                                        اضافة نوع اجازه
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <table id="vacation-table" class="display table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <!-- <th>action</th> -->
                                        <th style="width:150px;">العمليات</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade @if ($activeTab == 4) show active @endif" id="nav-gavernment"
                    role="tabpanel" aria-labelledby="nav-contact-tab">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                        aria-labelledby="nav-home-tab">
                        <div class="container  col-11 mt-3 p-0 ">
                            <div class="row justify-content-end">
                                <div class="col-auto">
                                    <button type="button" class="wide-btn  " data-bs-toggle="modal"
                                        id="extern-user-dev" data-bs-target="#add-gover" style="color: #0D992C;">
                                        <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                                        اضافة محافظه
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <table id="gavernment-table" class="display table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <!-- <th>action</th> -->
                                        <th style="width:150px;">العمليات</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- this for grade --}}

    <div class="modal fade" id="add-grade" tabindex="-1" aria-labelledby="extern-departmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة شخص صادر جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            &times;</button>
                    </div>
                </div>
                <div class="modal-body  d-flex justify-content-center mt-5 mb-5">
                    <form id="saveExternalUser" action="{{ route('grade.add') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nameus"> الاسم</label>
                            <input type="text" id="nameus" name="name" class="form-control" required>
                        </div>

                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">اضافة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- this for job add --}}
    {{-- <div class="modal fade" id="add-job" tabindex="-1" aria-labelledby="extern-departmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="extern-departmentLabel">إضافة مسمى وظيفى </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body  d-flex justify-content-center mt-5 mb-5">
                    <form id="saveExternalUser" action="{{ route('jobs.add') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name"> اسم الوظيفه</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="modal fade" id="add-job" tabindex="-1" aria-labelledby="extern-departmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة مسمى وظيفى </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            &times;</button>
                    </div>
                </div>
                <div class="modal-body  d-flex justify-content-center mt-5 mb-5">
                    <form id="saveExternalUser" action="{{ route('jobs.add') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nameus"> الاسم</label>
                            <input type="text" id="nameus" name="name" class="form-control" required>
                        </div>

                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">اضافة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- this for vacation type add --}}
    <div class="modal fade" id="add-type" tabindex="-1" aria-labelledby="extern-departmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة نوع اجازه جديد </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            &times;</button>
                    </div>
                </div>
                <div class="modal-body  d-flex justify-content-center mt-5 mb-5">
                    <form id="saveExternalUser" action="{{ route('vacationType.add') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="nameus"> الاسم</label>
                            <input type="text" id="nameus" name="name" class="form-control" required>
                        </div>

                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">اضافة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- this for government type add --}}
    <div class="modal fade" id="add-gover" tabindex="-1" aria-labelledby="extern-departmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة نوع اجازه جديد </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            &times;</button>
                    </div>
                </div>
                <div class="modal-body  d-flex justify-content-center mt-5 mb-5">
                    <form id="saveExternalUser" action="{{ route('vacationType.add') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="nameus"> الاسم</label>
                            <input type="text" id="nameus" name="name" class="form-control" required>
                        </div>

                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">اضافة</button>
                        </div>
                    </form>
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
                        <h5 class="modal-title" id="lable"> </h5>
                        <img src="../images/add-mandob.svg" alt="">
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body  d-flex justify-content-center mt-5 mb-5">
                    <form class="edit-grade-form" id="edit-form" action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="nameinput" value="" name="namegrade" class="form-control">
                            <input type="text" id="idinput" value="" name="id" hidden
                                class="form-control">
                            <input type="text" id="tab" value="" name="tab" hidden
                                class="form-control">

                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- this for delete form --}}
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="lable"> </h5>
                        <img src="../images/add-mandob.svg" alt="">
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body  d-flex justify-content-center mt-5 mb-5">
                    <form class="delete-grade-form" id="delete-form" action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">هل تريد مسح هذا العنصر ؟</label>
                            <input type="text" id="iddelete" value="" name="id" hidden
                                class="form-control">
                            <input type="text" id="tabActive" value="" name="tab" hidden
                                class="form-control">

                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#grade-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('setting/all/grade') }}', // Correct URL concatenation
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
                columnDefs: [{
                    targets: -1,
                    render: function(data, type, row) {
                        return `
                           <div class="form-row" dir="rtl">
                    <button type="button" class="btn-all mt-3" onclick=openedit(${row.id},'${row.name}',1)
                    style="background-color: #FAFBFD; border: none;">
                  <i class="fa fa-edit"></i>
                </button>
                </div>
                      <div class="form-
                      " dir="rtl">
                    <button type="button" class="btn-all mt-3" onclick=opendelete(${row.id},1)
                    style="background-color: #FAFBFD; border: none;">
                  <i class="fa fa-de"></i>
                </button>
                </div>
                `;
                    }
                }]
            });

        });

        function openedit(id, name, tab) {
            document.getElementById('nameinput').value = name;
            document.getElementById('idinput').value = id;
            document.getElementById('tab').value = tab;

            if (tab == 1) {
                document.getElementById('edit-form').setAttribute('action', '/grade');

                document.getElementById('lable').innerHTML = 'تعديل الرتبه';

            } else if (tab == 2) {
                document.getElementById('edit-form').setAttribute('action', '/jobs');

                document.getElementById('lable').innerHTML = 'تعديل الوظيفه';

            } else {
                document.getElementById('edit-form').setAttribute('action', '/vacationType');

                document.getElementById('lable').innerHTML = 'تعديل مسمى الاجازه';

            }

            $('#edit').modal('show');


        }

        function opendelete(id, tab) {
            document.getElementById('iddelete').value = id;
            document.getElementById('tabActive').value = tab;

            if (tab == 1) {
                document.getElementById('delete-form').setAttribute('action', '/grade/delete');

            } else if (tab == 2) {
                document.getElementById('delete-form').setAttribute('action', '/jobs/delete');

            } else {
                document.getElementById('delete-form').setAttribute('action', 'vacationType/delete');

            }
            $('#delete').modal('show');


        }
    </script>

    {{-- for jobs table --}}
    <script>
        $(document).ready(function() {
            $('#job-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('setting/all/job') }}', // Correct URL concatenation
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: -1,
                    render: function(data, type, row) {
                        return `
                               <div class="form-row" dir="rtl">
                    <button type="button" class="btn-all mt-3" onclick=openedit(${row.id},'${row.name}',2)
                    style="background-color: #FAFBFD; border: none;">
                  <i class="fa fa-edit"></i>
                </button>
                </div>
                   <div class="form-row" dir="rtl">
                    <button type="button" class="btn-all mt-3" onclick=opendelete(${row.id},2)
                    style="background-color: #FAFBFD; border: none;">
                  <i class="fa fa-delete"></i>
                </button>
                </div>
                        `;
                    }
                }]
            });
        });
    </script>
    {{-- for vacation table --}}
    <script>
        $(document).ready(function() {
            $('#vacation-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('setting/all/vacation') }}', // Correct URL concatenation
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: -1,
                    render: function(data, type, row) {
                        return `
                               <div class="form-row" dir="rtl">
                    <button type="button" class="btn-all mt-3" onclick=openedit(${row.id},'${row.name}',3)
                    style="background-color: #FAFBFD; border: none;">
                  <i class="fa fa-edit"></i>
                </button>
                </div>
                   <div class="form-row" dir="rtl">
                    <button type="button" class="btn-all mt-3" onclick=opendelete(${row.id},3)
                    style="background-color: #FAFBFD; border: none;">
                  <i class="fa fa-delete"></i>
                </button>
                </div>
                        `;
                    }
                }]
            });

        });
    </script>
    {{-- for gavernment table --}}
    <script>
        $(document).ready(function() {
            $('#gavernment-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('setting/all/government') }}', // Correct URL concatenation
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: -1,
                    render: function(data, type, row) {
                        return `
                               <div class="form-row" dir="rtl">
                    <button type="button" class="btn-all mt-3" onclick=openedit(${row.id},'${row.name}',3)
                    style="background-color: #FAFBFD; border: none;">
                  <i class="fa fa-edit"></i>
                </button>
                </div>
                   <div class="form-row" dir="rtl">
                    <button type="button" class="btn-all mt-3" onclick=opendelete(${row.id},3)
                    style="background-color: #FAFBFD; border: none;">
                  <i class="fa fa-delete"></i>
                </button>
                </div>
                        `;
                    }
                }]
            });

        });
    </script>
@endpush
