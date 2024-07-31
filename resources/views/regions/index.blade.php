@extends('layout.main')
@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@endpush
@section('title')
    المناطق
@endsection
@section('content')
    <section>
        <div class="row">

            <div class="container welcome col-11">
                <p> المنـــاطق</p>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">

                <div class="row " dir="rtl">
                    <div class="form-group mt-4  mx-md-2 col-12 d-flex ">
                        @if (Auth::user()->hasPermission('create Region'))
                        <button type="button" class="btn-all  "
                        onclick="openadd()" style="color: #0D992C;">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            اضافة جديد
                        </button>
                        @endif
                            <select name="government-select" id="government-select" class="form-group mx-md-2" onchange="filterRegions()">
                                <option value="">اختر المحافظه</option>
                                @foreach (getgovernments() as $government)
                                    <option value="{{ $government->id }}" @if($government->id == $id) selected @endif>{{ $government->name }}</option>
                                @endforeach
                            </select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="bg-white">
                        @if (session()->has('message'))
                        <div class="alert alert-info">
                            {{ session('message') }}
                        </div>
                    @endif
                        <div>
                            <table id="users-table" class="display table table-responsive-sm  table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>المحافظه التابعه لها</th>
                                        <th style="width:150px;">العمليات</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    {{-- this for add form --}}
    <div class="modal fade" id="add" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="lable"> أضافه منطقه جديد</h5>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form class="edit-grade-form" id="add-form" action=" {{ route('regions.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nameadd">الاسم</label>
                            <input type="text" id="nameadd" name="nameadd" class="form-control" required>
                            @error('nameadd')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="governmentid">المحافظات </label>
                            <select name="governmentid" id="governmentid" class="form-group col-md-12 mx-md-2" required>
                                <option value="">اختر المحافظه</option>
                                @foreach (getgovernments() as $government)
                                    <option value="{{ $government->id }}">{{ $government->name }}</option>
                                @endforeach
                            </select>
                            @error('government-id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue" onclick="confirmAdd()">اضافه</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- this for edit form --}}
    <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="lable"> تعديل اسم المنطقه ؟</h5>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form class="edit-grade-form" id="edit-form" action=" {{ route('regions.update') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="nameedit" value="" name="name" class="form-control" required>
                            <input type="text" id="idedit" value="" name="id" hidden class="form-control">

                        </div>
                        <div class="form-group">
                            <label for="government">المحافظات</label>
                            <select name="government" id="government" class="form-group col-md-12 mx-md-2" required>
                                <option value="">اختر المحافظه</option>
                                @foreach (getgovernments() as $government)
                                    <option value="{{ $government->id }}">{{ $government->name }}</option>
                                @endforeach
                            </select>
                            @error('government')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue" onclick="confirmEdit()">تعديل</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- model for delete form --}}
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="deleteModalLabel"> !تنبــــــيه</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                        </button>
                    </div>
                </div>
                <form id="delete-form" action="{{ route('regions.delete') }}" method="POST">
                    @csrf
                    <div class="modal-body  d-flex justify-content-center">
                        <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف هذه الرتبه ؟</h5>


                        <input type="text" id="id" hidden name="id" class="form-control">
                    </div>
                    <div class="modal-footer mx-2 d-flex justify-content-center">
                        <div class="text-end">
                            <button type="button" class="btn-blue">لا</button>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn-blue" onclick="confirmDelete()">نعم</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        // function opendelete(id) {
        //     document.getElementById('id').value = id;
        //     $('#delete').modal('show');
        // }

        // function confirmDelete() {
        //     var id = document.getElementById('id').value;
        //     var form = document.getElementById('delete-form');

        //     form.submit();

        // }

        function openedit(id, name,government) {
            document.getElementById('nameedit').value = name;
            document.getElementById('government').value = government;
            document.getElementById('idedit').value = id;

            $('#edit').modal('show');


        }

        function confirmEdit() {
            var id = document.getElementById('idedit').value;
            var name = document.getElementById('nameedit').value;
            var government = document.getElementById('government').value;
            var form = document.getElementById('edit-form');

            // form.submit();

        }
        function openadd() {
            $('#add').modal('show');
        }

        function confirmAdd() {
            var name = document.getElementById('nameadd').value;
            var government = document.getElementById('governmentid').value;
            var form = document.getElementById('add-form');

            // form.submit();

        }
        var table;
        $(document).ready(function() {
            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class
            government_id = $('#government-select').val();
            console.log(government_id);
            var table =$('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                url: '{{ route('getAllregions') }}',
                data: function(d) {
                d.government_id = $('#government-select').val(); // Add government_id to request
            }
            }, // Correct URL concatenation
                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'government_name',
                        name:  'government_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        sWidth: '100px',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[1, 'desc']],
                "oLanguage": {
                                "sSearch": "",
                                "sSearchPlaceholder":"بحث",
                                                                            "sInfo": 'اظهار صفحة _PAGE_ من _PAGES_',
                                            "sInfoEmpty": 'لا توجد بيانات متاحه',
                                            "sInfoFiltered": '(تم تصفية  من _MAX_ اجمالى البيانات)',
                                            "sLengthMenu": 'اظهار _MENU_ عنصر لكل صفحة',
                                            "sZeroRecords": 'نأسف لا توجد نتيجة',
                                            "oPaginate": {
                                                    "sFirst": "<<", // This is the link to the first page
                                                    "sPrevious": "<", // This is the link to the previous page
                                                    "sNext": ">", // This is the link to the next page
                                                    "sLast": " >>" // This is the link to the last page
                                                    }


                                        },
                                        layout: {
                                            bottomEnd: {
                                                paging: {
                                                    firstLast: false
                                                }
                                            }
                                        },
                                         "pagingType": "full_numbers"
            });

            $('#government-select').change(function() {
        table.ajax.reload(); // Reload DataTable data on dropdown change
    });
            });

            function filterRegions() {
              var  government_id = $('#government-select').val();
              
        if (window.table) {
            console.log('d');
            window.table.ajax.reload(); // Reload DataTable with new filter
        }
    }

    </script>
@endpush
