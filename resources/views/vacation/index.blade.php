@extends('layout.main')

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
                    <button type="button" class="wide-btn" href="{{ route('vacation.add', $id) }}">
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
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>military_number</th>
                            <th>action</th>
                        </tr>
                    </thead>
                </table>




                <script></script>


            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var id = {{ $id }};
            $('#vacations-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('employee.vacations', $id) }}', // Correct URL concatenation
                columns: [{
                        data: 'vacation_type_id',
                        name: 'vacation_type_id'
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
                        searchable: false,
                        render: function(data, type, row) {
                            console.log(row);
                            if (row) {
                                // Using route generation correctly in JavaScript
                                var editUrl = '{{ route('vacation.edit', ':id') }}';
                                var showUrl = '{{ route('vacation.show', ':id') }}';
                                var deleteUrl = '{{ route('vacation.delete', ':id') }}';

                                editUrl = editUrl.replace(':id', row.id);
                                showUrl = showUrl.replace(':id', row.id);
                                deleteUrl = deleteUrl.replace(':id', row.id);

                                // Checking if the vacation start date condition is met
                                var deleteButton = '';
                                var checkStartVacationDate = "<?php echo CheckStartVacationDate('" + row.id + "'); ?>";
                                if ({!! json_encode($checkStartVacationDate) !!}) {
                                    deleteButton =
                                        `<a href="${deleteUrl}" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>`;
                                }

                                return `<a href="${editUrl}" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
            <a href="${showUrl}" class="edit btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
            ${deleteButton}`;
                            }
                        }

                    }
                ]
            });
        });
    </script>
@endpush
