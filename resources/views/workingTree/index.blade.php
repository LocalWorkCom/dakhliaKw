@extends('layout.main')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title', 'نظام العمل')

@section('content')

    <div class="row ">
        <div class="container welcome col-11">
        <div class="d-flex justify-content-between">
            <p> نظام العمــــــــــل </p>
            <button type="button" class="wide-btn" style="color:#259240;"
                        onclick="window.location.href='{{ route('working_tree.add') }}'">
                     
                        اضافة نظام عمل جديد   <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                    </button>
        </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0  pt-5 pb-4">
            <!-- <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">
                  
                </div>
            </div> -->
            @include('inc.flash')

            <div class="col-lg-12">
                <div class="bg-white ">
                </div>


                <table id="users-table" class="display table table-responsive-sm  table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>عدد ايام العمل</th>
                            <th>عدد ايام الاجازات</th>
                            <th style="width:150px !important;">العمليات</th>
                        </tr>
                    </thead>
                </table>




                <script>
                    $(document).ready(function() {
                        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

                        $('#users-table').DataTable({
                            processing: true,
                            serverSide: true,
                            dom: 'lfrti',
                            ajax: '{{ route('working_trees') }}', // Correct URL concatenation
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
                                    data: 'working_days_num',
                                    name: 'working_days_num'
                                },
                                {
                                    data: 'holiday_days_num',
                                    name: 'holiday_days_num'
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

                                    // Using route generation correctly in JavaScript
                                    var editUrl = '{{ route('working_tree.edit', ':id') }}';
                                    var showUrl = '{{ route('working_tree.show', ':id') }}';

                                    editUrl = editUrl.replace(':id', row.id);
                                    showUrl = showUrl.replace(':id', row.id);
                                    var editButton = '';
                                    var showButton = '';

                                        editButton =
                                            `<a href="${editUrl}" class="edit btn  btn-sm" style="background-color: #259240;"><i class="fa fa-edit"></i> تعديل</a>`;
                                    
                                        showButton =
                                            `<a href="${showUrl}" class="edit btn  btn-sm" style="background-color: #375a97;"><i class="fa fa-eye"></i> عرض</a>`;
                                    
                                    // Checking if the vacation start date condition is met

                                    return `${editButton}${showButton}`;

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


            </div>
        </div>
    </div>
@endsection
