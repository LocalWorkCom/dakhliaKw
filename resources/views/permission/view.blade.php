@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>

@section('content')
    <section>
        <div class="row">
            <div class="container welcome col-11">
                <p>الصلاحيات</p>
            </div>
        </div>

        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row" dir="rtl">
                    <div class="form-group mt-4 mx-2 col-12 d-flex">
                        <button type="button" class="wide-btn"
                            onclick="window.location.href='{{ route('permission.create') }}'">
                            <img src="../images/add-btn.svg" alt="img"> اضافة جديد
                        </button>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="bg-white p-5">
                        <div>
                            <table id="users-table" class="display table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>رقم التعريف</th>
                                        <th>الصلاحية</th>
                                        <th>القسم</th>
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
                ajax: '{{ url('api/permission') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'guard_name', name: 'guard_name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                columnDefs: [{
                    targets: -1,
                    render: function(data, type, row) {

                        // Using route generation correctly in JavaScript
                        // var permissionedit = '{{ route('permissions_edit', ':id') }}';
                        // permissionedit = permissionedit.replace(':id', row.id);
                        var permissionshow = '{{ route('permissions_show', ':id') }}';
                        permissionshow = permissionshow.replace(':id', row.id);
                        var permissiondelete = '{{ route('permissions_destroy', ':id') }}';
                        permissiondelete = permissiondelete.replace(':id', row.id);
                        return `
                       <a href="` + permissionshow + `" class="btn btn-primary btn-sm w-25">مشاهدة</a>
                       <a href="` + permissiondelete + `" class="btn btn-primary btn-sm w-25">حذف</a>`;
                    }

                }]
            });
        });
    </script>
@endsection
{{-- <a href="` + permissionedit + `" class="btn btn-primary btn-sm">تعديل</a> --}}