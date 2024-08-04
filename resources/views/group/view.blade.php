@extends('layout.main')

@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer></script>
@endpush

@section('title')
    المجموعات
@endsection

@section('content')
    <section>
        <div class="row">
            <div class="container welcome col-11">
                <p> المجــــــــموعات</p>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row d-flex justify-content-between" dir="rtl">
                    <div class="form-group mt-4 mx-3 d-flex">
                        <button class="btn-all px-3" style="color: #274373;" onclick="openAddModal()" data-bs-toggle="modal" data-bs-target="#myModal1">
                            <img src="{{ asset('frontend/images/group-add.svg') }}" alt="">
                            اضافة مجموعة جديده
                        </button>
                        
                    </div>
                    <div class="form-group mt-4 mx-3 d-flex justify-content-end">
                        <button class="btn-all px-3" style="color: #FFFFFF; background-color: #274373;" onclick="window.print()">
                            <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
                        </button>
                    </div>
                </div>
                @if (session()->has('message'))
                <div class="alert alert-info">
                    {{ session('message') }}
                </div>
            @endif
                <div class="col-lg-12">
                    <div class="bg-white">
                        <div>
                            <table id="users-table" class="display table table-responsive-sm table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>رقم التسلسلي</th>
                                        <th>اسم المجموعة</th>
                                        <th>نظام العمل</th>
                                        <th>عدد المفتشيين</th>
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
                                        ajax: '{{ url('api/groups') }}',
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
                                                data: 'work_time_id',
                                                name: 'work_time_id'
                                            },
                                            {
                                                data: 'points_inspector',
                                                name: 'points_inspector'
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
                                                sFirst: "<< &nbsp;",
                                                sPrevious: "<&nbsp;",
                                                sNext: ">&nbsp;",
                                                sLast: "&nbsp; >>"
                                            }
                                        },
                                        pagingType: "full_numbers"
                                    });
                                });
                            </script>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Form Modal -->
    <div class="modal fade" id="myModal1" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">
                        <h5 class="modal-title"> اضافة مجموعة</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="add-form" action="{{ route('group.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nameadd">ادخل اسم المجموعة</label>
                            <input type="text" id="nameadd" name="name" class="form-control" placeholder="مجموعة أ" required>
                        </div>
                        <div class="mb-3">
                            <label for="work_time_id">اختر نظام العمل</label>
                            <select class="form-control" name="work_time_id" id="work_time_id">
                                <option selected disabled>اختار من القائمة</option>
                                @foreach($workTimes as $workTime)
                                <option value="{{ $workTime->id }}">{{ $workTime->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="points_inspector">عدد نقاط التفتيش</label>
                            <input type="number" id="points_inspector" name="points_inspector" class="form-control" placeholder="4" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn-all mx-2 p-2" style="background-color: #274373; color: #ffffff;">                                   
                            <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> اضافة
                            </button>
                            <button type="button" class="btn-all p-2" style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);" data-bs-dismiss="modal" aria-label="Close" data-bs-dismiss="modal">
                                <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                            </button>
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
                        <h5 class="modal-title" id="lable"> تعديل اسم المجموعة ؟</h5>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body  mt-5 mb-5">
                    <form class="edit-grade-form" id="edit-form" action=" {{ route('group.update') }}" method="POST">
                        @csrf
                        <div class="form-group ">
                            <label for="name">الاسم</label>
                            <input type="text" id="nameedit" value="" name="name" class="form-control" required>
                            <input type="text" id="idedit" value="" name="id" hidden class="form-control">

                        </div> 
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">تعديل</button>
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
            console.log("Document ready, initializing DataTable");
            $('#groups-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('groups.index') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'work_system', name: 'work_system' },
                    { data: 'inspection_points', name: 'inspection_points' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                language: {
                    search: "",
                    searchPlaceholder: "بحث",
                    info: 'اظهار صفحة _PAGE_ من _PAGES_',
                    infoEmpty: 'لا توجد بيانات متاحه',
                    infoFiltered: '(تم تصفية من _MAX_ اجمالى البيانات)',
                    lengthMenu: 'اظهار _MENU_ عنصر لكل صفحة',
                    zeroRecords: 'نأسف لا توجد نتيجة',
                    paginate: {
                        first: "<<",
                        previous: "<",
                        next: ">",
                        last: ">>"
                    }
                },
                pagingType: "full_numbers"
            });

            function openAddModal() {
                $('#myModal1').modal('show');
            }
        });
    </script>
@endpush
