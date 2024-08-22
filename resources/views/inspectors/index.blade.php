@extends('layout.main')

{{-- DataTables and jQuery --}}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer></script>

@section('content')
@section('title')
    المفتشون
@endsection

<section>
    <div class="row">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p id="inspector-heading">المفتــــــشون</p>
                {{-- @if (Auth::user()->hasPermission('edit grade')) --}}
                    <button type="button" class="btn-all" onclick="window.location.href='{{ route('inspectors.create') }}'" style="color: #0D992C;">
                        اضافة مفتش جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                    </button>
                {{-- @endif --}}
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="container col-11 mt-3 p-0">
            <div class="row d-flex justify-content-between" dir="rtl">
                <div class="form-group moftsh mt-4 mx-4 d-flex">
                    <p class="filter">تصفية حسب:</p>
                    <button class="btn-all px-3 mx-2 btn-filter btn-active" data-filter="all" style="color: #274373;">
                        الكل ({{ $all }})
                    </button>
                    <button class="btn-all px-3 mx-2 btn-filter" data-filter="assigned" style="color: #274373;">
                        مفتشون تم توزعهم ({{ $assignedInspectors }})
                    </button>
                    <button class="btn-all px-3 mx-2 btn-filter" data-filter="unassigned" style="color: #274373;">
                        مفتشون لم يتم توزعهم ({{ $unassignedInspectors }})
                    </button>
                </div>
                <div class="form-group mt-4 mx-4 d-flex justify-content-end">
                    <button class="btn-all px-3" style="color: #FFFFFF; background-color: #274373;" onclick="window.print()">
                        <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
                    </button>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="bg-white">
                    <div>
                        <table id="users-table" class="display table table-responsive-sm table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>رقم التعريف</th>
                                    <th>الرتبة</th>
                                    <th>الاسم</th>
                                    <th>رقم الهوية </th>
                                    <th>المجموعة </th>
                                    <th>رقم الهاتف </th>
                                    <th>النوع</th>
                                    <th class="action" style="width:150px !important;">العمليات</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- Add Form Modal -->
                    <div class="modal fade" id="myModal1" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-content-center">
                                    <div class="title d-flex flex-row align-items-center">
                                        <h5 class="modal-title">اضافة مجموعة</h5>
                                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                                </div>
                                <div class="modal-body mt-3 mb-3">
                                    <div class="container pt-5 pb-2" style="border: 0.2px solid rgb(166, 165, 165);">
                                        <form id="add-form" action="{{ route('inspectors.addToGroup') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="group_id" style="justify-content: flex-end;">اختر المجموعة</label>
                                                <select class="form-control select2" style="border: 0.2px solid rgb(199, 196, 196);width:100%;" name="group_id" id="group_id" required>
                                                    <option selected disabled>اختار من القائمة</option>
                                                    @foreach (getgroups() as $group)
                                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger span-error" id="group_id-error"></span>
                                                <input type="hidden" name="id" id="id" value="">
                                            </div>
                                            <div class="text-end pt-3">
                                                <button type="button" class="btn-all p-2" style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);" data-bs-dismiss="modal" aria-label="Close">
                                                    <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                                </button>
                                                <button type="submit" class="btn-all mx-2 p-2" style="background-color: #274373; color: #ffffff;">
                                                    <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> اضافة
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: transparent; border: none;">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                                </div>
                                <div class="modal-body mb-3 mt-3 d-flex justify-content-center">
                                    <div class="body-img-modal d-block">
                                        <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                                        <p>تمت الاضافه بنجاح</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

        var filter = 'all'; // Default filter

        const table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url('api/Inspectors') }}',
                dataSrc: function(json) {
                    if (filter === 'assigned') {
                        return json.data.filter(item => item.group_id != 'لا يوجد مجموعه للمفتش' && String(item.group_id).trim() !== "");
                    } else if (filter === 'unassigned') {
                        return json.data.filter(item => !item.group_id || (item.group_id === 'لا يوجد مجموعه للمفتش'));
                    }
                    return json.data;
                }
            },
            columns: [
                { data: 'id', sWidth: '50px', name: 'id' },
                { data: 'position', name: 'position' },
                { data: 'name', name: 'name' },
                { data: 'Id_number', name: 'Id_number' },
                { data: 'group_id', name: 'group_id' },
                { data: 'phone', name: 'phone' },
                { data: 'type', name: 'type' },
                {
                    data: 'action',
                    name: 'action',
                    sWidth: '200px',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[1, 'desc']],
            "oLanguage": {
                "sSearch": "",
                "sSearchPlaceholder": "بحث....",
                "sProcessing": "جاري البحث ....",
                "sZeroRecords": "لا يوجد سجلات",
                "sInfo": "عرض _START_ الى _END_ من _TOTAL_ مدخلات",
                "sLengthMenu": "اظهار _MENU_ مفتشين",
                "sEmptyTable": "لا توجد بيانات في هذا الجدول",
                "sInfoFiltered": " (تصفية من _MAX_ مجموع المدخلات)"
            },
            "search": {
                "regex": true
            }
        });

        $('.btn-filter').on('click', function() {
            $('.btn-filter').removeClass('btn-active');
            $(this).addClass('btn-active');
            filter = $(this).data('filter');
            table.ajax.reload();
        });

        // Select2 Initialization
        $('.select2').select2();

        $('#myModal1').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id'); // Extract info from data-* attributes
            var modal = $(this);
            modal.find('.modal-body #id').val(id); // Pass the id to hidden input in modal
        });
    });
</script>
@endpush
