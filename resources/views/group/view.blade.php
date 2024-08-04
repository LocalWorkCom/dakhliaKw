@extends('layout.main')

@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer></script>
@endpush

@section('title')
    المجموعات
@endsection

@section('content')
    <section>
        <div class="row">
            <div class="container welcome col-11">
                <p> المجــــــــموعات</p>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row d-flex justify-content-between" dir="rtl">
                    <div class="form-group mt-4 mx-3 d-flex">
                        <button class="btn-all px-3" style="color: #274373;" onclick="openAddModal()" data-bs-toggle="modal" data-bs-target="#myModal1">
                            <img src="../images/group-add.svg" alt="">
                            اضافة مجموعة جديده
                        </button>
                    </div>
                    <div class="form-group mt-4 mx-3 d-flex justify-content-end">
                        <button class="btn-all px-3" style="color: #FFFFFF; background-color: #274373;" onclick="window.print()">
                            <img src="../images/print.svg" alt=""> طباعة
                        </button>
                    </div>
                </div>

                <div class="col-lg-12" dir="rtl">
                    <div class="bg-white">
                        <div>
                            <table id="users-table" class="display table table-responsive-sm table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>رقم التسلسلي</th>
                                        <th>اسم المجموعة</th>
                                        <th>نظام العمل</th>
                                        <th>عدد المفتشيين</th>
                                        <th style="width:150px !important;">العمليات</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        @if (session()->has('message'))
                            <div class="alert alert-info">
                                {{ session('message') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Form Modal -->
    <div class="modal fade" id="myModal1" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <img src="../images/group-add-modal.svg" alt="">
                        <h5 class="modal-title"> اضافة مجموعة</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="add-form" action="{{ route('group.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nameadd">ادخل اسم المجموعة</label>
                            <input type="text" id="nameadd" name="name" class="form-control" placeholder="مجموعة أ" required>
                        </div>
                        <div class="mb-3">
                            <label for="work_time_id">اختر نظام العمل</label>
                            <select class="form-control" name="work_time_id" id="work_time_id">
                                @foreach($workTimes as $workTime)
                                <option value="{{ $workTime->id }}">{{ $workTime->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="points_inspector">عدد نقاط التفتيش</label>
                            <input type="number" id="points_inspector" name="points_inspector" class="form-control" placeholder="4" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">اضافه</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">الغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#groups-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('groups.index') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'work_system', name: 'work_system' },
                    { data: 'inspection_points', name: 'inspection_points' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                language: {
                    search: "",
                    searchPlaceholder: "بحث",
                    info: 'اظهار صفحة _PAGE_ من _PAGES_',
                    infoEmpty: 'لا توجد بيانات متاحه',
                    infoFiltered: '(تم تصفية من _MAX_ اجمالى البيانات)',
                    lengthMenu: 'اظهار _MENU_ عنصر لكل صفحة',
                    zeroRecords: 'نأسف لا توجد نتيجة',
                    paginate: {
                        first: "<<",
                        previous: "<",
                        next: ">",
                        last: ">>"
                    }
                },
                pagingType: "full_numbers"
            });

            function openAddModal() {
                $('#myModal1').modal('show');
            }
        });
    </script>
@endpush
