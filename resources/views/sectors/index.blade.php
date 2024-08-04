@extends('layout.main')
@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@endpush
@section('title')
    القطاعات
@endsection
@section('content')
        <div class="row">

            <div class="container welcome col-11">
                <p> القطاعـــات</p>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">
                <div class="row " dir="rtl">
                    <div class="form-group mt-4  mx-md-2 col-12 d-flex ">
                        @if (Auth::user()->hasPermission('create Sector'))
                        <button type="button" class="btn-all  "
                        onclick="window.location.href='{{ route('sectors.create') }}'" style="color: #0D992C;">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            اضافة جديد
                        </button>
                        @endif
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="bg-white">
                        @if (session()->has('message'))
                        <div class="alert alert-info">
                            {{ session('message') }}
                        </div>
                    @endif
                        <div>
                            <table id="users-table" class="display table table-responsive-sm  table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>المحافظه التابعه لها</th>
                                        <th style="width:150px;">العمليات</th>
                                    </tr>
                                </thead>
                            </table>
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
            var table =$('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                url: '{{ route('getAllsectors') }}',
            }, // Correct URL concatenation
                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'government_name',
                        name:  'government_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        sWidth: '100px',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[1, 'desc']],
                "oLanguage": {
                                "sSearch": "",
                                "sSearchPlaceholder":"بحث",
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
@endpush