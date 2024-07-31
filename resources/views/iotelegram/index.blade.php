@extends('layout.main')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title', 'الواردات')

@section('content')

    <div class="row">
        <div class="container welcome col-11">
            <p> الـــــــــــــــواردات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">
                    @if (Auth::user()->hasPermission('create Iotelegram'))
                        <button type="button" class="wide-btn"
                            onclick="window.location.href='{{ route('iotelegrams.add') }}'">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            اضافة جديد
                        </button>
                    @endif
                    @if (Auth::user()->hasPermission('archive Iotelegram'))
                        <button type="button" class="btn-all mx-3 "
                            onclick="window.location.href='{{ route('iotelegram.archives') }}'" style="color: #C1920C;">
                            <img src="{{ asset('frontend/images/archive-btn.svg') }}" alt="img">
                            عرض الارشيف
                        </button>
                    @endif
                </div>
            </div>
            @include('inc.flash')

            <div class="col-lg-12">
                <div class="bg-white ">
                </div>

                <table id="users-table" class="display table table-responsive-sm  table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>رقم الوارد</th>
                            <th>تاريخ الوارد</th>
                            <th>رقم الصادر</th>
                            <th>تاريخ الصادر</th>
                            <th>المندوب</th>
                            <th>القطاع</th>
                            <th>الموظف المستلم</th>
                            <th>النوع</th>
                            <th>عدد الكتب</th>
                            <th style="width:150px;">العمليات</th>
                        </tr>
                    </thead>
                </table>



                <script>
                    $(document).ready(function() {
                        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

                        $('#users-table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: '{{ route('iotelegrams.get') }}', // Correct URL concatenation
                            columns: [{
                                    data: 'iotelegram_num',
                                    sWidth: '120px',
                                    name: 'iotelegram_num'
                                },
                                {
                                    data: 'outgoing_date',
                                    name: 'outgoing_date'
                                },
                                {
                                    data: 'outgoing_num',
                                    name: 'outgoing_num'
                                },
                                {
                                    data: 'date',
                                    name: 'date'
                                },
                                {
                                    data: 'representive.name',
                                    name: 'representive.name'

                                },

                                {
                                    data: 'department',
                                    name: 'department'
                                },
                                {
                                    data: 'recieved.name',
                                    name: 'recieved.name'
                                },

                                {
                                    data: 'type',
                                    name: 'type'
                                },
                                {
                                    data: 'files_num',
                                    name: 'files_num'
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
                                    var addArchive = "<?php echo Auth::user()->hasPermission('add_archive Iotelegram'); ?>";
                                    var editIotelegram = "<?php echo Auth::user()->hasPermission('edit Iotelegram'); ?>";
                                    var showIotelegram = "<?php echo Auth::user()->hasPermission('view Iotelegram'); ?>";

                                    // Using route generation correctly in JavaScript
                                    var editUrl = '{{ route('iotelegram.edit', ':id') }}';
                                    var showUrl = '{{ route('iotelegram.show', ':id') }}';
                                    var archiveUrl = '{{ route('iotelegram.archive.add', ':id') }}';


                                    editUrl = editUrl.replace(':id', row.id);
                                    showUrl = showUrl.replace(':id', row.id);
                                    archiveUrl = archiveUrl.replace(':id', row.id);
                                    var archiveButton = '';
                                    var showButton = '';
                                    if (row.archives) {
                                        if (addArchive) {
                                            archiveButton =
                                                `<a href="${archiveUrl}" class="archive btn  btn-sm" onclick="confirmArchive(event, this)" style="background-color:#c1920c;"> <i class="fa-solid fa-file-arrow-up"></i> ارشفة</a>`;
                                        }
                                    } else {
                                        if (editIotelegram) {
                                            archiveButton =

                                                `<a href="${editUrl}" class="edit btn  btn-sm" style="background-color: #259240;"><i class="fa fa-edit"></i> تعديل</a>`;
                                        }
                                    }
                                    if (showIotelegram) {
                                        showButton =
                                            `<a href="${showUrl}" class="archive btn  btn-sm" style="background-color: #375a97;"><i class="fa fa-eye"></i> عرض</a>`;
                                    }
                                    // Checking if the vacation start date condition is met
                                    return `${showButton}${archiveButton}`;

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
                                    "sFirst": "<<", // This is the link to the first page
                                    "sPrevious": "<", // This is the link to the previous page
                                    "sNext": ">", // This is the link to the next page
                                    "sLast": " >>" // This is the link to the last page
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


            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmArchive(event, ele) {
            event.preventDefault();

            Swal.fire({
                title: 'تأكيد الأرشفة',
                text: "هل أنت متأكد أنك تريد نقل هذا العنصر إلى الأرشيف؟",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، أرشفه!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = $(ele).attr('href');
                }
            });
        }
    </script>
@endpush
