@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@push('style')
    <style>
        .custom-multiselect {
            position: relative;
            width: 100%;
            border: 0.2px solid rgb(199, 196, 196);
            border-radius: 5px;
            background-color: #fff;
        }

        .search-input {
            width: 100%;
            padding: 10px;
            border: none;
            border-bottom: 1px solid #ccc;
            outline: none;
            font-size: 16px;
            box-sizing: border-box;
            cursor: pointer;
            background-color: #fff;
        }

        .options-container {
            max-height: 200px;
            overflow-y: auto;
            display: none;
            position: absolute;
            top: 50px;
            width: 100%;
            background-color: #fff;
            z-index: 100;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .options-container .option {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            cursor: pointer;
        }

        .options-container .option:hover {
            background-color: #f0f0f0;
        }
    </style>
@endpush
@section('title')
    أنواع المخالفات
@endsection
@section('content')
    <section>
        <div class="row">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p id = "violation-type-heading"> أنواع المخالفـــات</p>
                    {{-- @if (Auth::user()->hasPermission('create VacationType')) --}}
                    <button type="button" class="btn-all  " onclick="openadd()" style="color: #0D992C;">

                        اضافة نوع مخالفه <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                    </button>
                    {{-- @endif --}}
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 pt-5 pb-4">
                <div class="row d-flex justify-content-between " dir="rtl">

                    <div class="form-group moftsh mt-4  mx-4  d-flex">
                        <p class="filter "> تصفية حسب:</p>
                        <button class="btn-all px-3 mx-2 btn-filter btn-active" data-filter="all" style="color: #274373;">
                            الكل ({{ $all }})
                        </button>
                        <div class="form-group moftsh select-box-2 mx-3  d-flex">
                            <h4 style="line-height: 1.8;"> المخالفه : </h4>
                            <select id="filter" name="filter"
                                class="form-control custom-select custom-select-lg mb-3 select2">
                                <option value="" data-filter="">كل المخالفات</option>
                                @foreach (getDepartments() as $item)
                                    <option value="{{ $item->id }}" data-filter="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <button class="btn-all px-3 mx-2 btn-filter" data-filter="buildings" style="color: #274373;">
                            ادارة مباني ({{ $buildings }})
                        </button>
                        <button class="btn-all px-3 mx-2 btn-filter" data-filter="behavior" style="color: #274373;">
                            سلوك انضباطي ({{ $behavior }})
                        </button> --}}
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="bg-white ">
                        @if (session()->has('message'))
                            <div class="alert alert-info">
                                {{ session('message') }}
                            </div>
                        @endif
                        <div>
                            <table id="users-table"
                                class="display table table-responsive-sm  table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>نوع المخالفه</th>

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
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center ">
                        <h5 class="modal-title"> اضافة مخالفه </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form class="edit-grade-form" id="add-form" action=" {{ route('violations.store') }}" method="POST">
                        @csrf
                        <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                            <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                <div class="form-group mt-4 mb-3">
                                    <label class="d-flex justify-content-start pt-3 pb-2" for="name"
                                        style=" flex-direction: row-reverse;"> اسم
                                        المخالفه</label>
                                    <input type="text" id="nameadd" name="nameadd" class="form-control"
                                        placeholder="اسم المخالفه" dir="rtl" required>
                                    @if ($errors->has('nameadd'))
                                        <span class="text-danger span-error" id="nameadd-error"
                                            dir="rtl">{{ $errors->first('nameadd') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <label class="d-flex justify-content-start pb-2" for="types"
                                        style="flex-direction: row-reverse;">
                                        الاداره الخاصه بالمخالفه
                                    </label>
                                    <div class="custom-multiselect">
                                        <input type="text" id="selectedItems" placeholder="اختر الاداره..."
                                            class="search-input" readonly onclick="toggleOptions()">
                                        <div class="options-container" id="optionsContainer">
                                            @foreach (getDepartments() as $department)
                                                <label class="option">
                                                    <input type="checkbox" value="{{ $department->id }}" name="types[]"
                                                        onchange="updateSelectedItems()">
                                                    {{ $department->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>


                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2" dir="rtl">
                                    <button type="submit" class="btn-all mx-2 p-2"
                                        style="background-color: #274373; color: #ffffff;" id="openSecondModalBtn">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> اضافة
                                    </button>
                                    <button type="button" class="btn-all p-2"
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                    <!-- Second Modal Body (Initially Hidden) -->
                    <div id="secondModalBody" class="d-none">
                        <div class="body-img-modal d-block mb-4">
                            <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                            <p>تمت الاضافه بنجاح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- this for edit form --}}
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title">تعديل المخالفه</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <form class="edit-grade-form" id="edit-form" action="{{ route('violations.update') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" id="idedit" name="id">
                        <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                            <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                <div class="form-group mt-4 mb-3">
                                    <label class="d-flex justify-content-start pt-3 pb-2" for="nameedit"
                                        style=" flex-direction: row-reverse;">اسم
                                        المخالفه</label>
                                    <input type="text" id="nameedit" name="nameedit" class="form-control"
                                        placeholder="اسم المخالفه" required>
                                </div>

                                <div class="form-group  mb-3" style="height: 142px;">
                                    <label class="d-flex justify-content-start pb-2" for="types"
                                        style=" flex-direction: row-reverse;">
                                        الاداره الخاصه بالمخالفه</label>
                                        <select name="types[]" id="types" multiple
                                        class=" form-control custom-select custom-select-lg mb-3 select2 col-12"
                                        style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;" required dir="rtl">
                                    {{-- <select class="w-100 px-2 select2" name="types[]" id="types" multiple
                                        style="border: 0.2px solid rgb(199, 196, 196);" required dir="rtl"> --}}
                                        @foreach (getDepartments() as $department)
                                            <option value="{{ $department->id }}"> {{ $department->name }}</option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2" dir="rtl">
                                    <button type="submit" class="btn-all mx-2 p-2"
                                        style="background-color: #274373; color: #ffffff;" id="openSecondModalBtn">
                                        <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> تعديل
                                    </button>
                                    <button type="button" class="btn-all p-2"
                                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="secondModalBody" class="d-none">
                        <div class="body-img-modal d-block mb-4">
                            <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                            <p>تمت الاضافه بنجاح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
          $('.select2').select2({
            dir: "rtl"
        });
       
        function openedit(id, name, selectedTypesJson) {
            console.log('Opening edit modal for ID:', id);
            console.log('Selected Types JSON:', selectedTypesJson);

            let selectedTypes;
            try {
                // Directly parse the JSON without additional replacements
                selectedTypes = JSON.parse(selectedTypesJson);
            } catch (e) {
                console.error('Error parsing selected types JSON:', e);
                return; // Exit if JSON parsing fails
            }

            // Set modal fields
            document.getElementById('idedit').value = id;
            document.getElementById('nameedit').value = name;

            // Multi-select handling
            let multiSelectElement = document.getElementById('types');
            Array.from(multiSelectElement.options).forEach(option => {
                option.selected = selectedTypes.includes(option.value); // Set selection based on parsed JSON
            });
            $('#types').val(selectedTypes).trigger('change');

            // Open modal
            $('#edit').modal('show');
        }

        function confirmEdit() {
            var id = document.getElementById('id').value;
            document.getElementById('types').value = types;

            var form = document.getElementById('edit-form');

            // form.submit();
        }

        function openadd() {
            $('#add').modal('show');
        }


        $(document).ready(function() {


            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class
            var filter = 'all'; // Default filter


            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                bResetDisplay: true,
                ajax: {
                    url: '{{ route('violations.getAllviolations') }}',
                    data: function(d) {

                        d.filter = filter;
                    }
                },
                columns: [{
                        data: 'name',
                        sWidth: '50px',
                        name: 'name'
                    },
                    {
                        data: 'type_name',
                        sWidth: '50px',
                        name: 'type_name'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        sWidth: '100px',
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
                    console.log('Page ' + this.api().page.info().pages)
                    var page = this.api().page.info().pages;
                    console.log($('#users-table tr').length);
                    if (page === 1) {
                        $('.dataTables_paginate').css('visibility',
                            'hidden'); // Hide pagination if only one page
                    } else {
                        $('.dataTables_paginate').css('visibility',
                            'visible'); // Show pagination if more than one page
                    }
                }

            });

            var defaultFilterButton = $('.btn-filter[data-filter="all"]');
            var defaultFilterText = defaultFilterButton.text().trim();
            $('#violation-type-heading').text('انواع المخالفات - ' + defaultFilterText);
            $('#filter').change(function() {
                const selectedOption = $(this).find('option:selected'); // Get the selected option
                var filterText = selectedOption.text().trim(); // Get the text of the selected option
                filter = selectedOption.data('filter'); // Get the data-filter value


                // Update the heading text
                $('#violation-type-heading').text('انواع المخالفات - ' + filterText);

                // Reset to the first page and reload the table
                table.page(0).draw(true);
                table.ajax.reload();
            });

        });
    </script>

    <script>
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

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('add-form').addEventListener('submit', function(event) {
                event.preventDefault();

                var name = document.getElementById('nameadd').value;
                var checkboxes = document.querySelectorAll('input[name="types[]"]');
                var typesSelected = Array.from(checkboxes).some(checkbox => checkbox.checked);

                var form = document.getElementById('add-form');
                var inputs = form.querySelectorAll('[required]');
                var valid = true;

                // Clear previous error messages and styles
                inputs.forEach(function(input) {
                    input.style.borderColor = ''; // Reset border color
                });

                var selectBox = document.getElementById('select-box');
                if (selectBox) {
                    selectBox.style.borderColor = ''; // Reset border color
                }

                // Validate required inputs
                inputs.forEach(function(input) {
                    if (!input.value) {
                        valid = false;
                        input.style.borderColor = 'red'; // Highlight empty inputs
                        alert('من فضلك ادخل اسم نوع الشكوى');
                    }
                });

                // Validate checkboxes
                if (!typesSelected) {
                    valid = false;
                    if (selectBox) {
                        selectBox.style.borderColor = 'red'; // Highlight empty inputs
                    }
                    alert('من فضلك اختر الأداره الخاصه بالشكوى');
                }

                // If all validations pass, submit the form
                if (valid) {
                    this.submit();
                }
            });
        });
    </script>
    <script>
        // Show/Hide options on input focus
        // Toggle the visibility of the options container
        function toggleOptions() {
            const optionsContainer = document.getElementById('optionsContainer');
            optionsContainer.style.display =
                optionsContainer.style.display === 'none' || optionsContainer.style.display === '' ?
                'block' :
                'none';
        }

        // Hide options container when clicking outside
        document.addEventListener('click', function(e) {
            const optionsContainer = document.getElementById('optionsContainer');
            const selectedItems = document.getElementById('selectedItems');
            if (!optionsContainer.contains(e.target) && e.target !== selectedItems) {
                optionsContainer.style.display = 'none';
            }
        });

        // Update the input with selected items
        function updateSelectedItems() {
            const selectedItems = document.getElementById('selectedItems');
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            const selectedValues = Array.from(checkboxes).map(cb => cb.parentElement.textContent.trim());

            // Display the selected items in the input field
            selectedItems.value = selectedValues.join(', ');
        }
    </script>
@endpush
