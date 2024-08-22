@extends('layout.main')

@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
    </script>
@endpush

@section('title')
    المجموعات
@endsection

@section('content')
    <section>


        <div class="row">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> المجــــــــموعات</p>
                    <button class="btn-all px-3" style="color: #274373;" data-bs-toggle="modal" data-bs-target="#myModal1">
                        اضافة مجموعة جديده
                        <img src="{{ asset('frontend/images/group-add.svg') }}" alt="">
                    </button>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row d-flex justify-content-between" dir="ltr">

                    <div class="form-group mt-4 mx-3 d-flex justify-content-end">
                        <button class="btn-all px-3" style="color: #FFFFFF; background-color: #274373;"
                            onclick="window.print()">
                            <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
                        </button>
                    </div>
                </div>
                @if (session('success'))
                    <div class="alert alert-success mt-2">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="col-lg-12">
                    <div class="bg-white">
                        <div>
                            <table id="users-table"
                                class="display table table-responsive-sm table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>رقم التسلسلي</th>
                                        <th>اسم المجموعة</th>
                                        <th>اسم قطاع</th>
                                        <th>عدد الدوريات</th>
                                        <th>عدد المفتشيين</th>
                                        <th>عدد النقاط لكل دورية فى اليوم</th>

                                        <th style="width:150px !important;">العمليات</th>
                                    </tr>
                                </thead>
                            </table>
                            <script>
                                $(document).ready(function() {
                                    $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm';

                                    $('#users-table').DataTable({
                                        processing: true,
                                        serverSide: true,
                                        ajax: '{{ url('api/groups') }}',
                                        bAutoWidth: false,
                                        columns: [{
                                                data: 'id',
                                                sWidth: '50px',
                                                name: 'id'
                                            },
                                            {
                                                data: 'name',
                                                name: 'name'
                                            },
                                            {
                                                data: 'sector.name',
                                                name: 'sector.name'
                                            },

                                            {
                                                data: 'num_team',
                                                name: 'num_team'
                                            },
                                            {
                                                data: 'num_inspectors',
                                                name: 'num_inspectors'
                                            },

                                            {
                                                data: 'points_inspector',
                                                name: 'points_inspector'
                                            },

                                            {
                                                data: 'action',
                                                name: 'action',
                                                sWidth: '200px',
                                                orderable: false,
                                                searchable: false
                                            }
                                        ],
                                        columnDefs: [{
                                            targets: -1,
                                            render: function(data, type, row) {
                                                return `
                                                 <a href="#" class="btn btn-sm " style="background-color: #274373;" onclick="openViewModal('${row.id}', '${row.name}')"> <i class="fa fa-eye"></i>عرض  </a>
                                                 <a href="#" class="btn btn-sm" style="background-color: #F7AF15;" onclick="openEditModal('${row.id}', '${row.name}')"> <i class="fa fa-edit"></i> تعديل </a>


                                                `;
                                            }
                                        }],
                                        oLanguage: {
                                            sSearch: "",
                                            sSearchPlaceholder: "بحث",
                                            sInfo: 'اظهار صفحة _PAGE_ من _PAGES_',
                                            sInfoEmpty: 'لا توجد بيانات متاحه',
                                            sInfoFiltered: '(تم تصفية من _MAX_ اجمالى البيانات)',
                                            sLengthMenu: 'اظهار _MENU_ عنصر لكل صفحة',
                                            sZeroRecords: 'نأسف لا توجد نتيجة',
                                            oPaginate: {
                                                "sFirst": '<i class="fa fa-fast-backward" aria-hidden="true"></i>', // This is the link to the first page
                                                "sPrevious": '<i class="fa fa-chevron-left" aria-hidden="true"></i>', // This is the link to the previous page
                                                "sNext": '<i class="fa fa-chevron-right" aria-hidden="true"></i>', // This is the link to the next page
                                                "sLast": '<i class="fa fa-step-forward" aria-hidden="true"></i>' // This is the link to the last page
                                            }
                                        },
                                        pagingType: "full_numbers"
                                    });
                                });
                            </script>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Form Modal -->
    <div class="modal fade" id="myModal1" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="padding-left: 0px;" dir="rtl">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">
                        <h5 class="modal-title"> اضافة مجموعة</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <form id="add-form" action="{{ route('group.store') }}" method="POST">
                                @csrf
                                <div class="form-group mt-4 mb-3">
                                    <label for="nameadd" class="d-flex justify-content-start pt-3 pb-2">ادخل اسم
                                        المجموعة</label>
                                    <input type="text" id="nameadd" name="name" class="form-control"
                                        placeholder=" اكتب المجموعة" value="{{ old('name') }}">
                                    @if ($errors->has('name'))
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>

                                <div class="form-group mt-4 mb-3">
                                    <label class="d-flex justify-content-start pt-3 pb-2" for="sector_id"> اختر
                                        القطاع </label>
                                    <select name="sector_id" id="sector_id" class="form-control"
                                        style="border: 0.2px solid rgb(199, 196, 196);">
                                        <option value="">قطاع </option>
                                        @foreach ($sectors as $sector)
                                            <option value="{{ $sector->id }}">{{ $sector->name }} </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('sector_id'))
                                        <span class="text-danger">{{ $errors->first('sector_id') }}</span>
                                    @endif
                                </div>


                                <div class="form-group mt-4 mb-3">
                                    <label for="points_inspector" class="d-flex justify-content-start pt-3 pb-2">عدد النقاط
                                        لكل دورية فى اليوم</label>
                                    <input type="number" id="points_inspector" name="points_inspector" class="form-control"
                                        placeholder="1"
                                        value="{{ old('points_inspector') ? old('points_inspector') : 1 }}">
                                    @if ($errors->has('points_inspector'))
                                        <span class="text-danger">{{ $errors->first('points_inspector') }}</span>
                                    @endif
                                </div>
                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                    <button type="submit" class="btn-all mx-2 p-2"
                                        style="background-color: #274373; color: #ffffff;">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> اضافة
                                    </button>
                                    <button type="button" class="btn-all p-2"
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                                {{-- @if (session('success'))
                                    <div class="alert alert-success mt-2">
                                        {{ session('success') }}
                                    </div>
                                @endif --}}


                            </form>
                        </div>
                    </div>
                    <!-- Second Modal Body (Initially Hidden) -->
                    <div id="secondModalBody" class="d-none">
                        <div class="body-img-modal d-block mb-4">
                            <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                            <p>تمت الاضافه بنجاح</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Second Modal Body (Initially Hidden) -->
            <div id="secondModalBody" class="d-none">
                <div class="body-img-modal d-block mb-4">
                    <img src="../images/ordered.svg" alt="">
                    <p>تمت الاضافه بنجاح</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if (session('showModal'))
            $(document).ready(function() {
                $('#myModal1').modal('show');

            });
        @endif
    </script>


    <!-- view Form Modal -->
    <div class="modal fade" id="view" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true"
        style="padding-left: 0px;" dir="rtl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center ">
                        <h5 class="modal-title"> عرض المجموعة </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">

                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <div class="form-group mt-4 mb-3">
                                <label class="d-flex justify-content-start pt-3 pb-2" for="nameadd_show">
                                    اسم المجموعة </label>
                                <input type="text" id="nameadd_show" name="nameadd_show" class="form-control"
                                    placeholder="اكتب المجموعة" disabled>
                            </div>
                            <div class="form-group mt-4 mb-3">
                                <label class="d-flex justify-content-start pt-3 pb-2" for="sector_id"> اختر القطاع
                                </label>
                                <select name="sector_id" id="sector_show_id" class="form-control " disabled
                                    style="border: 0.2px solid rgb(199, 196, 196);">
                                    <option value="">قطاع </option>
                                    @foreach ($sectors as $sector)
                                        <option value="{{ $sector->id }}">{{ $sector->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="d-flex justify-content-start pb-2" for="points_inspector_show">
                                    عدد النقاط لكل دورية فى اليوم </label>

                                <input type="number" id="points_inspector_show" name="points_inspector_show"
                                    class="form-control" disabled>
                            </div>


                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="padding-left: 0px;" dir="rtl">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">
                        <h5 class="modal-title"> تعديل مجموعة</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <form id="add-form" action="{{ route('group.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="id_edit" name="id_edit">
                                <div class="form-group mt-4 mb-3">
                                    <label for="name_edit" class="d-flex justify-content-start pt-3 pb-2">ادخل اسم
                                        المجموعة</label>
                                    <input type="text" id="name_edit" name="name_edit" class="form-control"
                                        placeholder=" اكتب المجموعه" value="{{ old('name_edit') }}">
                                    @if ($errors->has('name_edit'))
                                        <span class="text-danger">{{ $errors->first('name_edit') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mt-4 mb-3">
                                    <label class="d-flex justify-content-start pt-3 pb-2" for="sector_id"> اختر
                                        القطاع </label>
                                    <select name="sector_id" id="sector_edit_id" class="form-control select2"
                                        style="border: 0.2px solid rgb(199, 196, 196);">
                                        <option value="">القطاع </option>
                                        @foreach ($sectors as $sector)
                                            <option value="{{ $sector->id }}">{{ $sector->name }} </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('sector_id'))
                                        <span class="text-danger">{{ $errors->first('sector_id') }}</span>
                                    @endif
                                </div>

                                <div class="form-group mt-4 mb-3">
                                    <label for="points_inspector_edit" class="d-flex justify-content-start pt-3 pb-2">عدد
                                        النقاط لكل دورية فى اليوم </label>
                                    <input type="number" id="points_inspector_edit" name="points_inspector_edit"
                                        class="form-control" value="{{ old('points_inspector_edit') }}">
                                    @if ($errors->has('points_inspector_edit'))
                                        <span class="text-danger">{{ $errors->first('points_inspector_edit') }}</span>
                                    @endif
                                </div>

                                <span class="text-danger span-error">
                                    @if ($errors->has('nothing_updated'))
                                        {{ $errors->first('nothing_updated') }}
                                    @endif
                                </span>

                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                    <button type="submit" class="btn-all mx-2 p-2"
                                        style="background-color: #274373; color: #ffffff;">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> تعديل
                                    </button>
                                    <button type="button" class="btn-all p-2"
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle modal display -->
    <script>
        @if (session('editModal'))
            $(document).ready(function() {
                $('#edit').modal('show');
            });
        @endif

        @if (session('message'))
            $(document).ready(function() {
                alert('{{ session('message') }}');
            });
        @endif
    </script>

    <!-- Team Modal -->
    {{-- <div class="modal fade" id="team" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="padding-left: 0px;" dir="rtl">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">
                        <h5 class="modal-title"> اضافة دورية لمجموعة</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <form action="{{ route('groupTeam.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="group_id" id="group_id">
                                <div class="form-group mt-4 mb-3">
                                    <label for="groupTeam_name" class="d-flex justify-content-start pt-3 pb-2">ادخل اسم
                                        الدورية</label>
                                    <input type="text" id="groupTeam_name" name="groupTeam_name" class="form-control"
                                        placeholder="دورية أ" required>
                                </div>

                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                    <button type="submit" class="btn-all mx-2 p-2"
                                        style="background-color: #274373; color: #ffffff;">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> الاضافة
                                    </button>
                                    <button type="button" class="btn-all p-2"
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close" data-bs-dismiss="modal">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@push('scripts')
    {{-- <script>
        $(document).ready(function() {
            console.log("Document ready, initializing DataTable");
            $('#groups-table').DataTable({
                processing: true,
                serverSide: true,
                ajax:'{{ url('api/groups') }}',
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
    </> --}}
    <script>
        function openViewModal(id, name) {
            // console.log("id", id);
            $.ajax({
                url: '/groups/show/' + id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        // console.log("data",data);

                        // // Populate modal fields with data
                        document.getElementById('nameadd_show').value = data.group.name;
                        document.getElementById('points_inspector_show').value = data.group.points_inspector;
                        document.getElementById('sector_show_id').value = data.group.sector_id;
                        // document.getElementById('id_show').value = data.id;
                        $('#view').modal('show');
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error retrieving data');
                }
            });
        }

        function openEditModal(id, name) {

            $.ajax({
                url: '/groups/edit/' + id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        // Populate modal fields with data
                        document.getElementById('name_edit').value = data.group.name;
                        document.getElementById('points_inspector_edit').value = data.group.points_inspector;
                        document.getElementById('id_edit').value = data.group.id;

                        // let select = document.getElementById('types');
                        // let options = select.options;

                        // // Clear previous selections
                        // for (let i = 0; i < options.length; i++) {
                        //     options[i].selected = false;
                        // }

                        // // Set new selections
                        // for (let i = 0; i < options.length; i++) {
                        //     console.log('Option value:', options[i].value); // Debugging
                        //     if (types.includes(parseInt(options[i].value))) {
                        //         options[i].selected = true;
                        //         console.log('Option value:', options[i].value); // Debugging

                        //     }
                        // }

                        document.getElementById('sector_edit_id').value = data.group.sector_id;
                        $('#sector_edit_id').val(data.group.sector_id).change();


                        // Select the option in the dropdown


                        $('#edit').modal('show');
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error retrieving data');
                }
            });


        }



        function openTeamModal(id) {
            $.ajax({
                url: '/groupTeam/team/' + id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        console.log("dd", data);
                        document.getElementById('group_id').value = id;
                        // Empty the container
                        $('#permissions-container').empty();

                        // Generate checkboxes
                        data.forEach(function(item) {
                            var checkbox = $('<div class="form-check"></div>');
                            var input = $('<input>')
                                .attr({
                                    type: 'checkbox',
                                    id: 'exampleCheck' + item.id,
                                    value: item.id,
                                    name: 'inspectors_ids[]',
                                    class: 'form-check-input selectPermission',
                                    style: 'width: 25px; height: 25px; margin-left: 1px;'
                                });
                            var label = $('<label>')
                                .attr({
                                    class: 'form-check-label m-1',
                                    for: 'exampleCheck' + item.id
                                })
                                .css('font-size', '20px')
                                .text(item.name); // or use translation if needed

                            checkbox.append(input).append(label);
                            $('#permissions-container').append(checkbox);
                        });

                        // Show the modal
                        $('#team').modal('show');
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error retrieving data');
                }
            });
        }
        // document.addEventListener('DOMContentLoaded', function() {
        //     var openSecondModalBtn = document.getElementById('openSecondModalBtn');
        //     var firstModalBody = document.getElementById('firstModalBody');
        //     var secondModalBody = document.getElementById('secondModalBody');

        //     openSecondModalBtn.addEventListener('click', function() {
        //         firstModalBody.classList.add('d-none');
        //         secondModalBody.classList.remove('d-none');
        //     });
        // });
    </script>
    <script>
        $('.select2').select2({
            // dir: "rtl"
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Get elements
            var openSecondModalBtn = document.getElementById('openSecondModalBtn');
            var firstModalBody = document.getElementById('firstModalBody');
            var secondModalBody = document.getElementById('secondModalBody');

            // Add click event listener
            openSecondModalBtn.addEventListener('click', function() {
                // Hide the first modal body
                firstModalBody.classList.add('d-none');

                // Show the second modal body
                secondModalBody.classList.remove('d-none');
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const selectBox = document.getElementById('select-box');
            const options = document.getElementById('options');
            const searchInput = document.getElementById('search-input');
            const selectedValuesContainer = document.getElementById('selected-values');
            const optionCheckboxes = document.querySelectorAll('.option input[type="checkbox"]');
            selectBox.addEventListener('click', function() {
                options.style.display = options.style.display === 'block' ? 'none' : 'block';
            });
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.select-wrapper')) {
                    options.style.display = 'none';
                }
            });
            optionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const selectedOptions = Array.from(optionCheckboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.nextElementSibling.textContent);
                    selectedValuesContainer.innerHTML = selectedOptions.join(', ');
                });
            });
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                optionCheckboxes.forEach(checkbox => {
                    const optionLabel = checkbox.nextElementSibling.textContent.toLowerCase();
                    checkbox.parentElement.style.display = optionLabel.includes(searchTerm) ?
                        'block' : 'none';
                });
            });
        });
    </script>
@endpush
