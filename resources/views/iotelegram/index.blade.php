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
                    <button type="button" class="wide-btn" onclick="window.location.href='{{ route('iotelegrams.add') }}'">
                        <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                        اضافة جديد
                    </button>
                    <button type="button" class="btn-all mx-3 "
                        onclick="window.location.href='{{ route('iotelegram.archives') }}'" style="color: #C1920C;">
                        <img src="{{ asset('frontend/images/archive-btn.svg') }}" alt="img">
                        عرض الارشيف
                    </button>


                </div>
            </div>
            @include('inc.flash')

            <div class="col-lg-12">
                <div class="bg-white p-5">
                </div>

                <table id="iotelegrams-table" class="display table table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>التاريخ</th>
                            <th>المندوب</th>
                            <th>الجهة المرسلة</th>
                            <th>الموظف المستلم</th>
                            <th> عدد الفايلات</th>
                            <th>النوع</th>
                            <th>الخيارات</th>
                        </tr>
                    </thead>
                </table>



                <script>
                    $(document).ready(function() {
                        $('#iotelegrams-table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: '{{ route('iotelegrams.get') }}', // Correct URL concatenation
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
                                    data: 'files_num',
                                    name: 'files_num'
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
                                    var editUrl = '{{ route('iotelegram.edit', ':id') }}';
                                    var showUrl = '{{ route('iotelegram.show', ':id') }}';
                                    var archiveUrl = '{{ route('iotelegram.archive.add', ':id') }}';


                                    editUrl = editUrl.replace(':id', row.id);
                                    showUrl = showUrl.replace(':id', row.id);
                                    archiveUrl = archiveUrl.replace(':id', row.id);

                                    // Checking if the vacation start date condition is met
                                    var archiveButton = (row.archives) ?
                                        `<a href="${archiveUrl}" class="archive btn btn-success btn-sm" onclick="confirmArchive(event, this)"> <i class="fa fa-archive"></i> </a>` :
                                        `<a href="${editUrl}" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a>`;


                                    return `<a href="${editUrl}" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a><a href="${showUrl}" class="edit btn btn-info btn-sm"><i class="fa fa-eye"></i></a>${archiveButton}`;

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
