@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>

@section('content')
@section('title')
    المفتشون
@endsection
<section>

    <div class="row">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p id="inspector-heading"> المفتــــــشون</p>
                {{-- <p> المفتــــــشون ({{\App\Models\Inspector::count()}})</p> --}}
                {{-- @if (Auth::user()->hasPermission('edit grade')) --}}
                <button type="button" class="btn-all  " onclick="window.location.href='{{ route('inspectors.create') }}'"
                    style="    color: #0D992C;">

                    اضافة مفتش جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                </button>
                {{-- @endif --}}
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="container  col-11 mt-3 pb-4 p-0 ">
            <div class="row d-flex justify-content-between " dir="rtl">
                <div class="form-group moftsh mt-4  mx-4  d-flex">
                    <p class="filter "> تصفية حسب:</p>
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
                <div class="form-group mt-4 mx-4  d-flex justify-content-end ">
                    <button class="btn-all px-3 " style="color: #FFFFFF; background-color: #274373;"
                        onclick="window.print()">
                        <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
                    </button>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('showModal'))
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Show the modal
                        $('#myModal').modal('show');
                    });
                    setTimeout(function() {
                        $('#myModal').modal('hide');
                    }, 30000);
                </script>
            @endif


            <div class="col-lg-12">

                <div class="bg-white ">
                    <div>
                        <table id="users-table"
                            class="display table table-responsive-sm  table-bordered table-hover dataTable">
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
                    <!-- Transfer Form Modal -->
                    <div class="modal fade" id="TranferMdel" tabindex="-1" aria-labelledby="myModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-content-center">
                                    <div class="title d-flex flex-row align-items-center">
                                        <h5 class="modal-title"> التحويل لموظف</h5>
                                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">

                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close">&times;</button>
                                </div>
                                <div class="modal-body mt-3 mb-3">
                                    <div class="container pt-5 pb-2" style="border: 0.2px solid rgb(166, 165, 165);">
                                        <form id="transfer-form" action="{{ route('inspectors.remove') }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="id_employee" id="id_employee" value="">
                                            <div class="mb-3">
                                                <label style="justify-content: flex-end;"> هل انت متأكد من تحويل المفتش
                                                    لموظف ؟ </label>
                                            </div>
                                            <div class="text-end pt-3">
                                                <button type="button" class="btn-all p-2 "
                                                    style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                                    data-bs-dismiss="modal" aria-label="Close" data-bs-dismiss="modal">
                                                    {{-- <img src="{{ asset('frontend/images/red-close.svg') }}"
                                                      alt="img"> --}}
                                                    لا
                                                </button>
                                                <button type="submit" class="btn-all mx-2 p-2"
                                                    style="background-color: #274373; color: #ffffff;">
                                                    {{-- <img src="{{ asset('frontend/images/white-add.svg') }}"
                                                      alt="img"> --}}
                                                    نعم
                                                </button>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add Form Modal -->
                    <div class="modal fade" id="myModal1" tabindex="-1" aria-labelledby="myModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-content-center">
                                    <div class="title d-flex flex-row align-items-center">
                                        <h5 class="modal-title"> اضافة مجموعة</h5>
                                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">

                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close">&times;</button>
                                </div>
                                <div class="modal-body mt-3 mb-3">
                                    <div class="container pt-5 pb-2" style="border: 0.2px solid rgb(166, 165, 165);">
                                        <form id="add-form" action="{{ route('inspectors.addToGroup') }}"
                                            method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="group_id" style="justify-content: flex-end;">اختر
                                                    المجموعه
                                                </label>
                                                <select class="form-control select2"
                                                    style="border: 0.2px solid rgb(199, 196, 196);width:100%;"
                                                    name="group_id" id="group_id" required>
                                                    <option selected disabled>اختار من القائمة</option>
                                                    @foreach (getgroups() as $group)
                                                        <option value="{{ $group->id }}">{{ $group->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger span-error" id="group_id-error"></span>
                                                <input type="hidden" name="id" id="id" value="">
                                            </div>
                                            <div class="text-end pt-3">
                                                <button type="button" class="btn-all p-2 "
                                                    style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                                    data-bs-dismiss="modal" aria-label="Close"
                                                    data-bs-dismiss="modal">
                                                    <img src="{{ asset('frontend/images/red-close.svg') }}"
                                                        alt="img">
                                                    الغاء
                                                </button>
                                                <button type="submit" class="btn-all mx-2 p-2"
                                                    style="background-color: #274373; color: #ffffff;">
                                                    <img src="{{ asset('frontend/images/white-add.svg') }}"
                                                        alt="img">
                                                    اضافة
                                                </button>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog"
                        aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: transparent; border: none;">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        &times;</button>
                                    </button>
                                </div>
                                <div class="modal-body mb-3 mt-3 d-flex justify-content-center">
                                    <div class="body-img-modal d-block ">
                                        <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                                        <p>تمت العمليه بنجاح</p>
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
    $('#group_id').select2({
        width: '100%',
        minimumResultsForSearch: 0,
        dropdownParent: $('#myModal1'), // Ensures dropdown stays within modal bounds
        placeholder: 'اختار من القائمة',
        allowClear: true
    });
    $(document).ready(function() {
        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

        var filter = 'all'; // Default filter

        const table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url('api/Inspectors') }}',
                data: function(d) {
                    d.filter = filter; // Send the filter to the server
                }
            },
            columns: [{
                    data: 'id',
                    sWidth: '50px',
                    name: 'id'
                },
                {
                    data: 'position',
                    name: 'position'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'Id_number',
                    name: 'Id_number'
                },
                {
                    data: 'group_id',
                    name: 'group_id'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'action',
                    name: 'action',
                    sWidth: '200px',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [1, 'desc']
            ],

            "oLanguage": {
                "sSearch": "",
                "sSearchPlaceholder": "بحث",
                "sInfo": 'اظهار صفحة _PAGE_ من _PAGES_',
                "sInfoEmpty": 'لا توجد بيانات متاحه',
                "sInfoFiltered": '(تم تصفية  من _MAX_ اجمالى البيانات)',
                "sLengthMenu": 'اظهار _MENU_ عنصر لكل صفحة',
                "sZeroRecords": 'نأسف لا توجد نتيجة',
                "oPaginate": {
                    "sFirst": '<i class="fa fa-fast-backward" aria-hidden="true"></i>', // This is the link to the first page
                    "sPrevious": '<i class="fa fa-chevron-left" aria-hidden="true"></i>', // This is the link to the previous page
                    "sNext": '<i class="fa fa-chevron-right" aria-hidden="true"></i>', // This is the link to the next page
                    "sLast": '<i class="fa fa-step-forward" aria-hidden="true"></i>' // This is the link to the last page
                }
            },
            layout: {

                bottomEnd: {
                    paging: {
                        firstLast: false
                    }
                }
            },
            "pagingType": "full_numbers",
            "fnDrawCallback": function(oSettings) {
                    var api = this.api();
                    var pageInfo = api.page.info();

                    // Check if the total number of records is less than or equal to the number of entries per page
                    if (pageInfo.recordsTotal <= 10) { // Adjust this number based on your page length
                        $('.dataTables_paginate').css('visibility', 'hidden'); // Hide pagination
                    } else {
                        $('.dataTables_paginate').css('visibility', 'visible'); // Show pagination
                    }
                }
        });

        var defaultFilterButton = $('.btn-filter[data-filter="all"]');
        var defaultFilterText = defaultFilterButton.text().trim();
        $('#inspector-heading').text('المفتــــــشون - ' + defaultFilterText);
        $('.btn-filter').click(function() {
            filter = $(this).data('filter'); // Update the filter variable
            var filterText = $(this).text().trim(); // Get the text of the active button

            // Remove 'btn-active' class from all buttons and add to the clicked one
            $('.btn-filter').removeClass('btn-active');
            $(this).addClass('btn-active');

            $('#inspector-heading').text('المفتــــــشون - ' + filterText); // Update the <p> content

            table.page(0).draw(true); // Reload table data
            table.ajax.reload();

        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#add-form').on('submit', function(event) {
            const groupId = $('#group_id').val();
            const errorSpan = $('#group_id-error'); // Get the error span element
            errorSpan.text(''); // Clear any existing error message

            if (!groupId) {
                event.preventDefault();
                errorSpan.text('يرجى اختيار مجموعة قبل الإضافة.'); // Set the error message

                return false;
            }
        });
    });

    function openAddModal(id, idGroup) {
        $('#myModal1').modal('show');
        document.getElementById('id').value = id;

        console.log(id);
        // Set the selected value for group_id
        var selectElement = document.getElementById('group_id');
        selectElement.value = idGroup;

        // Optionally, if you want to ensure the option is selected if the value doesn't match
        for (var i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value == idGroup) {
                selectElement.selectedIndex = i;
                break;
            }
        }
    }

    function openTransferModal(id) {
        $('#TranferMdel').modal('show');
        document.getElementById('id_employee').value = id;
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Set a 30-second timer to fade out and remove alerts
        setTimeout(function() {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');

            if (successAlert) {
                successAlert.classList.remove('show');
                successAlert.classList.add('fade');
                setTimeout(() => successAlert.remove(), 500); // Delay removal for fade effect
            }
            if (errorAlert) {
                errorAlert.classList.remove('show');
                errorAlert.classList.add('fade');
                setTimeout(() => errorAlert.remove(), 500); // Delay removal for fade effect
            }
        }, 300); // 30 seconds
    });
</script>
@endpush
