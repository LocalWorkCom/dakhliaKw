@extends('layout.main')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title', 'الاجازات')

@section('content')

    <div class="row ">
        <div class="container welcome col-11">
            <p> الاجــــــازات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">
                    <button type="button" class="wide-btn"
                        onclick="window.location.href='{{ route('vacation.add', $id) }}'">
                        <img src="../images/add-btn.svg" alt="img">
                        اضافة جديد
                    </button>

                </div>
            </div>
            @include('inc.flash')

            <div class="col-lg-12">
                <div class="bg-white p-5">
                </div>


                <table id="vacations-table" class="display table table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>نوع الاجازة</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ النهاية</th>
                            <th>الخيارات</th>
                        </tr>
                    </thead>
                </table>




                <script>
                    $(document).ready(function() {
                        var id = {{ $id }};
                        $('#vacations-table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: '{{ route('employee.vacations', $id) }}', // Correct URL concatenation
                            columns: [{
                                    data: 'id',
                                    name: 'id'
                                },
                                {
                                    data: 'vacation_type.name',
                                    name: 'vacation_type.name'
                                },
                                {
                                    data: 'date_from',
                                    name: 'date_from'
                                },
                                {
                                    data: 'date_to',
                                    name: 'date_to'
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
                                    var editUrl = '{{ route('vacation.edit', ':id') }}';
                                    var showUrl = '{{ route('vacation.show', ':id') }}';
                                    var deleteUrl = '{{ route('vacation.delete', ':id') }}';

                                    editUrl = editUrl.replace(':id', row.id);
                                    showUrl = showUrl.replace(':id', row.id);
                                    deleteUrl = deleteUrl.replace(':id', row.id);

                                    // Checking if the vacation start date condition is met
                                    var deleteButton = (row.StartVacation) ?
                                        `<a href="${deleteUrl}" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>` :
                                        '';


                                    return `<a href="${editUrl}" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a><a href="${showUrl}" class="edit btn btn-info btn-sm"><i class="fa fa-eye"></i></a>${deleteButton}`;

                                }

                            }]
                        });
                    });
                </script>


            </div>
        </div>
    </div>
@endsection
