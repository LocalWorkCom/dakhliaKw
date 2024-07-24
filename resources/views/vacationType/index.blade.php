@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title')
    أنواع الأجازات
@endsection
@section('content')
    <section>
        <div class="row">

            <div class="container welcome col-11">
                <p> أنواع الاجـــــازات</p>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">

                <div class="row " dir="rtl">
                    <div class="form-group mt-4  mx-2 col-12 d-flex ">
                        <button type="button" class="btn-all  "
                            onclick="openadd()" style="color: #0D992C;">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            اضافة جديد
                        </button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="bg-white p-5">

                        <div>
                            <table id="users-table" class="display table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
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
    {{-- this for add form --}}
    <div class="modal fade" id="add" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="lable"> أضافه نوع أجازه جديد</h5>
                        <img src="../images/add-mandob.svg" alt="">
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form class="edit-grade-form" id="add-form" action=" {{ route('vacationType.add') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="nameadd" name="nameadd" class="form-control">

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
                        <h5 class="modal-title" id="lable"> تعديل اسم الأجازه ؟</h5>
                        <img src="../images/add-mandob.svg" alt="">
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form class="edit-grade-form" id="edit-form" action=" {{ route('vacationType.update') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="nameedit" value="" name="name" class="form-control">
                            <input type="text" id="idedit" value="" name="id" hidden class="form-control">

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
                <form id="delete-form" action="{{ route('vacationType.delete') }}" method="POST">
                    @csrf
                    <div class="modal-body  d-flex justify-content-center">
                        <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف هذه الاجازه ؟</h5>


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
        function opendelete(id) {
            document.getElementById('id').value = id;
            $('#delete').modal('show');
        }

        function confirmDelete() {
            var id = document.getElementById('id').value;
            var form = document.getElementById('delete-form');

            form.submit();

        }

        function openedit(id, name) {
            document.getElementById('nameedit').value = name;
            document.getElementById('idedit').value = id;

            $('#edit').modal('show');


        }

        function confirmEdit() {
            var id = document.getElementById('id').value;
            var form = document.getElementById('edit-form');

            form.submit();

        }
        function openadd() {
            $('#add').modal('show');
        }

        function confirmAdd() {
            var name = document.getElementById('nameadd').value;
            var form = document.getElementById('add-form');

            form.submit();

        }
        $(document).ready(function() {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('setting.getAllvacationType') }}', // Correct URL concatenation
                columns: [{
                        data: 'name',
                        name: 'name'
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
