@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title')
الأرشيف
@endsection
@section('content')
    <section>
        <div class="row col-11" dir="rtl">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('Export.index') }}">الصادرات </a></li>
                    <li class="breadcrumb-item active" aria-current="page"> <a href=""> الأرشيف </a></li>
                </ol>
            </nav>
        </div>
        <div class="row ">
            <div class="container welcome col-11">
                <p> الصــــــــــــادرات </p>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 pt-5">
                <div class="col-lg-12">
                    <div class="bg-white ">

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
