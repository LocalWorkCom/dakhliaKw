@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('content')
    <section>
        <div class="row">
            @if (url()->current() == url('/users/0'))
                <div class="container welcome col-11">
                    <p>المستخـــــــــــدمين</p>
                </div>
            @elseif (url()->current() == url('/employees/1'))
                <div class="container welcome col-11">
                    <p>الموظفين</p>
                </div>
            @endif
        </div>


        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">
                <div class="row " dir="rtl">
                    <div class="form-group mt-4  mx-2 col-12 d-flex ">
                        <button type="button" class="wide-btn"
                            onclick="window.location.href='{{ route('user.create', $id) }}'">
                            <img src="../images/add-btn.svg" alt="img">
                            اضافة جديد
                        </button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="bg-white p-5">
                        <!-- <div>
                                            <a href="{{ route('user.create', $id) }}" class="btn btn-lg bg-primary text-white" dir="rtl">
                                                اضافه جديد</a>
                                        </div>
                                        <br> -->

                        <div>
                            <table id="users-table" class="display table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>رقم التعريف</th>
                                        <th>الاسم</th>
                                        <th>الهاتف</th>
                                        <th>الرقم العسكري</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                            </table>




                            <script>
                                $(document).ready(function() {
                                    var id = {{ $id }};
                                    $('#users-table').DataTable({
                                        processing: true,
                                        serverSide: true,
                                        ajax: '{{ url('api/users') }}/' + id, // Correct URL concatenation
                                        columns: [{
                                                data: 'id',
                                                name: 'id'
                                            },
                                            {
                                                data: 'name',
                                                name: 'name'
                                            },
                                            {
                                                data: 'phone',
                                                name: 'phone'
                                            },
                                            {
                                                data: 'military_number',
                                                name: 'military_number'
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
                                                var useredit = '{{ route('user.edit', ':id') }}';
                                                useredit = useredit.replace(':id', row.id);
                                                var usershow = '{{ route('user.show', ':id') }}';
                                                usershow = usershow.replace(':id', row.id);
                                                var vacation='{{ route('vacations.list',':id') }}';
                                                vacation = vacation.replace(':id', row.id);

                                                return `
                                        <a href="` + usershow + `" class="btn btn-primary btn-sm">مشاهدة</a>
                                        <a href="` + useredit + `" class="btn btn-primary btn-sm">تعديل</a>
                                        <a href="${vacation}">الاجازات</a>  <br> <hr>

                                        `;
                                            }

                                        }]
                                    });
                                });
                            </script>


                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection
