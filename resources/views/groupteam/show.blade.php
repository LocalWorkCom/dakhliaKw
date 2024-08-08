@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>

@section('content')
@section('title')
    الفرق
@endsection
<section>
    <div class="row">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">

                <p>الفرق</p>
                <p></p>
                <div class="second mx-4">
                    <button class="btn-all px-3 mx-2" style="color: #274373;">
                        <a href="{{ route('groupTeam.transfer', $id) }}" style="color: #274373">

                            نقل مفتشين <img src="{{ asset('frontend/images/change.svg') }}" class="mx-1">
                        </a>
                    </button>
                    {{-- <button class="btn-all px-3 mx-2" style="color: #259240;">
                        <a href="{{ route('group.groupcreateInspectors', $id) }}" style="color: #259240">

                            اضافة مفتش جديد <img src="{{ asset('frontend/images/add-green.svg') }}" class="mx-1">
                        </a>
                    </button> --}}
                    <button class="btn-all px-3 mx-2" style="color: #259240;" data-bs-toggle="modal"
                        data-bs-target="#myModal1">
                        اضافة فريق <img src="{{ asset('frontend/images/green-group.svg') }}" class="mx-1">
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" dir="rtl">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">
                        <h5 class="modal-title"> اضافة فريق</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- First Modal Body -->
                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <form action="{{ route('groupTeam.store', $id) }}" method="POST">
                                @csrf
                                <div class="form-group mt-4 mb-3">
                                    <label class="d-flex justify-content-start pt-3 pb-2" for="groupTeam_name"> ادخل اسم
                                        الفريق </label>
                                    <input type="text" id="groupTeam_name" name="groupTeam_name"
                                        class="form-control @error('groupTeam_name') is-invalid @enderror"
                                        placeholder="ادخل الفريق" value="{{ old('groupTeam_name') }}">
                                    @error('groupTeam_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group mt-4 mb-3">
                                    <label for="working_tree_id" class="d-flex justify-content-start pt-3 pb-2">اختر
                                        نظام العمل</label>
                                    <select class="form-control" name="working_tree_id" id="working_tree_id">
                                        <option selected disabled>اختار من القائمة</option>
                                        @foreach ($workTrees as $workTree)
                                            <option value="{{ $workTree->id }}"
                                                {{ old('working_tree_id') == $workTree->id ? 'selected' : '' }}>
                                                {{ $workTree->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('working_tree_id'))
                                        <span class="text-danger">{{ $errors->first('working_tree_id') }}</span>
                                    @endif
                                </div>
                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                    <button type="submit" class="btn-all mx-2 p-2"
                                        style="background-color: #274373; color: #ffffff;">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> الاضافة
                                    </button>
                                    <button type="button" class="btn-all p-2"
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Script to Reopen Modal if there are Validation Errors -->
                    @if ($errors->any())
                        <script>
                            $(document).ready(function() {
                                $('#myModal1').modal('show');
                            });
                        </script>
                    @endif

                    <!-- Second Modal Body (Initially Hidden) -->
                    <div id="secondModalBody" class="d-none">
                        <div class="body-img-modal d-block">
                            <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                            <p>تمت الاضافه بنجاح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="container col-11 mt-3 py-4">
            @if (session('success'))
                <div class="alert alert-success mt-2">
                    {{ session('success') }}
                </div>
            @endif
            {{-- <div class="row" dir="rtl">
                    <div class="form-group mt-4 mx-md-2 col-12 d-flex">
                        <button type="button" class="wide-btn"
                            onclick="window.location.href='{{ route('permission.create') }}'">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">  
                        </button>
                    </div>
                </div> --}}

            <div class="col-lg-12">
                <div class="bg-white ">
                    <div>
                        <table id="users-table"
                            class="display table table-responsive-sm  table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>رقم التعريف</th>
                                    <th>الاسم</th>

                                    <th>عدد المفتشين</th>
                                    <th>نظام العمل</th>

                                    <th>المجموعة</th>
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
<script>
    $(document).ready(function() {
        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('api.getGroupTeam', $id) }}',
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'inspectorCount',
                    name: 'inspectorCount'
                },
                {
                    data: 'working_tree.name',
                    name: 'working_tree.name'
                },
                {
                    data: 'group.name',
                    sname: 'group.name'
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

                    // Using route generation correctly in JavaScript
                    var teamEdit = '{{ route('groupTeam.edit', ':id') }}';
                    teamEdit = teamEdit.replace(':id', row.id);
                    var teamShow = '{{ route('groupTeam.show', ':id') }}';
                    teamShow = teamShow.replace(':id', row.id);
                    // var permissionshow = '{{ route('permissions_show', ':id') }}';
                    // permissionshow = permissionshow.replace(':id', row.id);
                    // var permissiondelete = '{{ route('permissions_destroy', ':id') }}';
                    // permissiondelete = permissiondelete.replace(':id', row.id);
                    return `
                       <a href="` + teamShow + `" class="btn btn-sm" style="background-color: #274373;"> <i class="fa fa-eye"></i>عرض  </a>
                       <a href="` + teamEdit + `" class="btn btn-sm"  style="background-color: #F7AF15;"> <i class="fa fa-edit"></i> تعديل او اضافه مفتش </a>
                     
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
            "pagingType": "full_numbers"

        });
    });
</script>
@endsection
{{-- <a href="` + permissionedit + `" class="btn btn-primary btn-sm">تعديل</a> --}}
