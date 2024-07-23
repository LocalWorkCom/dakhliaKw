@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('content')
    <section>
        <div class="row">

            <div class="container welcome col-11">
                <p> الصـــــــــادرات</p>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">

                <div class="row " dir="rtl">
                    <div class="form-group mt-4  mx-2 col-12 d-flex ">
                        <button type="button" class="btn-all mx-3 "
                            onclick="window.location.href='{{ route('Export.index') }}'" style="color: #C1920C;">
                            <img src="{{ asset('frontend/images/archive-btn.svg') }}" alt="img">
                            رجوع
                        </button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="bg-white p-5">

                        <div>
                            <table id="users-table" class="display table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>رقم الصادر</th>
                                        <th>الاسم</th>
                                        <th>الملاحظات</th>
                                        <th>تاريخ الصادر</th>
                                        <th> العسكرى</th>
                                        <th>الاداره الصادر لها</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('Export.view.archive') }}', // Correct URL concatenation
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'note',
                        name: 'note'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'person_to_username',
                        name: 'person_to_username'
                    },
                    {
                        data: 'department_External_name',
                        name: 'department_External_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],

            });
        });
    </script>
@endpush
