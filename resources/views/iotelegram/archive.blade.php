@extends('layout.main')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title', 'الارشيف')

@section('content')
<div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('iotelegrams.list') }}">الواردات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="">الارشيف</a></li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="container welcome col-11">
            <p> الارشيـــــــــــــف </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 pt-5 ">
            <!-- <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">
                    <button type="button" class="wide-btn" onclick="window.location.href='{{ route('iotelegrams.add') }}'">
                        <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                        اضافة جديد
                    </button>
                </div>
            </div> -->
            @include('inc.flash')

            <div class="col-lg-12 mb-2" >
                <div class="bg-white ">
                </div>

                <table id="users-table" class="display table table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>التاريخ</th>
                            <th>المندوب</th>
                            <th>الجهة المرسلة</th>
                            <th>الموظف المستلم</th>
                            <th>النوع</th>
                            <th>الخيارات</th>
                        </tr>
                    </thead>
                </table>



                <script>
                    $(document).ready(function() {
                        $('#users-table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: '{{ route('iotelegram.archives.get') }}', // Correct URL concatenation
                            columns: [{
                                    data: 'id',
                                    name: 'id'
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
                                    data: 'recieved_by.name',
                                    name: 'recieved_by.name'
                                },
                            
                                {
                                    data: 'type',
                                    name: 'type'
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
                                    var showUrl = '{{ route('iotelegram.show', ':id') }}';
                                    showUrl = showUrl.replace(':id', row.id);

                                    return `<a href="${showUrl}" class="edit btn btn-info btn-sm" style="background-color: #375a97;"><i class="fa fa-eye"></i></a>`;

                                }

                            }]
                        });
                    });
                </script>


            </div>
        </div>
    </div>

@endsection
@push('scripts')
@endpush
