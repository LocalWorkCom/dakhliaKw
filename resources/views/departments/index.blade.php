



@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>

@section('content')
@section('title')
    عرض
@endsection
    <section>
        <div class="row">
        <div class="container welcome col-11">
                <p> الــــــــــادارات </p>
            </div>
        </div>

        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row" dir="rtl">
                    <div class="form-group mt-4 mx-2 col-12 d-flex">
                        <button type="button" class="wide-btn mx-3"
                            onclick="window.location.href='{{ route('departments.create') }}'">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img"> اضافة جديد
                        </button>

                        <button type="button" class="wide-btn mx-3"
                            onclick="window.location.href='{{ route('postmans.create') }}'">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img"> اضافة مندوب
                        </button>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="bg-white ">
                        <div>
                            <table id="users-table" class="display table table-responsive-sm table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>رقم التعريف</th>
                                        <th>الاسم</th>
                                        <th>المدير</th>
                                        <th>مساعد المدير</th>
                                        <th>الاقسام</th>
                                        <th>الوارد</th>
                                        <th>الصادر</th>
                                        <th>إجراء</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
    $(document).ready(function() {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url('api/department') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'manger', name: 'manger' },  // Ensure 'manager' column exists
                { data: 'manger_assistance', name: 'manger_assistance' },  // Ensure 'manager_assistant' column exists
                { data: 'children_count', name: 'children_count' },
                { data: 'iotelegrams_count', name: 'iotelegrams_count' },
                { data: 'outgoings_count', name: 'outgoings_count' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [{
                targets: -1,
                render: function(data, type, row) {
                    var departmentEdit = '{{ route('departments.edit', ':id') }}';
                    departmentEdit = departmentEdit.replace(':id', row.id);
                    var departmentShow = '{{ route('departments.show', ':id') }}';
                    departmentShow = departmentShow.replace(':id', row.id);
                    var departmentDelete = '{{ route('departments.destroy', ':id') }}';
                    departmentDelete = departmentDelete.replace(':id', row.id);

                    return `
                    <a href="${departmentEdit}" class="btn  btn-sm" style="background-color: #259240;"><i class="fa fa-edit"></i></a>
                        <a href="${departmentShow}" class="btn  btn-sm w-25" style="background-color: #375A97;"><i class="fa fa-eye"></i></a>
                        <a href="${departmentDelete}" class="btn  btn-sm w-25" style="background-color: #C91D1D;"><i class="fa-solid fa-trash"></i></a>`;
                }
            }]
        });
    });
    </script>

@endsection
<!-- {{-- <a href="` + permissionedit + `" class="btn  btn-sm" style="background-color: #259240;"><i class="fa fa-edit"></i</a> --}} -->
