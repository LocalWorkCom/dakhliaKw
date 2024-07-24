@extends('layout.main')
@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@endpush
@section('title')
    المحـــافظات
@endsection
@section('content')
    <section>
        <div class="row">

            <div class="container welcome col-11">
                <p> المحـــافظات</p>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">

                <div class="row " dir="rtl">
                    <div class="form-group mt-4  mx-2 col-12 d-flex ">
                        <button type="button" class="btn-all  "
                            onclick="window.location.href='{{ route('government.create') }}'" style="color: #0D992C;">
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

    {{-- model for add to archive  --}}
    {{-- <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="delete"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="delete"> هل تريد حذف هذه المحافظه ؟</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="saveExternalDepartment" action="{{ route('government.delete') }}" method="POST">
                        @csrf
                            <input type="text" id="id" hidden name="id" class="form-control">
                       
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">نعم</button>
                        </div>
                         <!-- Save button -->
                         <div class="text-end">
                            <button type="button" class="btn-black">لا</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('setting.getAllgovernment') }}', // Correct URL concatenation
                columns: [
                    {
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

            function opendelete(id) {
            document.getElementById('id').value = id;
            $('#delete').modal('show');


        }

        });
    </script>
@endpush
