@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@push('style')

@endpush
@section('title')
أنواع المخالفات
@endsection
@section('content')
<section>
    <div class="row">

        <div class="container welcome col-11">
        <div class="d-flex justify-content-between">
            <p> أنواع المخالفـــات</p>
            {{-- @if (Auth::user()->hasPermission('create VacationType')) --}}

<button class="btn-all px-3" style="color: #274373;" onclick="openadd()" data-bs-toggle="modal"
    data-bs-target="#myModal1">
    اضافة مخالفه     <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="">
</button>
{{-- @endif --}}
        </div>
    </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">

            <!-- <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-md-2 col-12 d-flex ">
                    {{-- @if (Auth::user()->hasPermission('create VacationType')) --}}

                    <button class="btn-all px-3" style="color: #274373;" onclick="openadd()" data-bs-toggle="modal"
                        data-bs-target="#myModal1">
                        <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="">
                        اضافة مخالفه
                    </button>
                    {{-- @endif --}}
                </div>
            </div> -->
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
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
            <div class="modal-body" >
                <form class="edit-grade-form" id="add-form" action=" {{ route('violations.store') }}" method="POST">
                    @csrf
                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <div class="form-group mt-4 mb-3">
                                <label class="d-flex justify-content-start pt-3 pb-2" for="name" 
                                style="display:flex ; flex-direction:column-reverse;"> اسم
                                    المخالفه</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="اسم المخالفه"
                                    required>

                            </div>

                            <div class="form-group  mb-3">
                                <div class="select-wrapper">
                                    <div class="select-box d-flex justify-content-between" id="select-box">
                                        <p> قسم الخاص بالمخالفه</p>
                                        <i class="fa-solid fa-angle-down" style="color: #A3A1A1;"></i>
                                    </div>
                                    <div class="options" id="options">
                                        <div class="search-box">
                                            <input type="text" id="search-input" placeholder="ابحث هنا ....."
                                                style="width: 100% !important;">
                                        </div>
                                        @foreach (getDepartments() as $department)
                                        <div class="option">
                                            <input type="checkbox" id="option{{ $department->id }}"
                                                value="{{ $department->id }}" name="types[]">
                                            <label for="option{{ $department->id }}"    style="display:flex ; flex-direction:column-reverse;"> {{ $department->name }}
                                            </label>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>
                                <div id="selected-values" class="mt-2"></div>
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
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center">
                <div class="title d-flex flex-row align-items-center">
                    <h5 class="modal-title">تعديل المخالفه</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <form class="edit-grade-form" id="edit-form" action="{{ route('violations.update') }}" method="POST">
                    @csrf
                    <input type="hidden" id="idedit" name="id">
                    <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                        <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                            <div class="form-group mt-4 mb-3">
                                <label class="d-flex justify-content-start pt-3 pb-2" for="nameedit"    
                                style="display:flex ; flex-direction:column-reverse;">اسم
                                    المخالفه</label>
                                <input type="text" id="nameedit" name="name" class="form-control"
                                    placeholder="اسم المخالفه" required>
                            </div>
                            <div class="form-group  mb-3" dir="rtl">
                                <label class="d-flex justify-content-start pb-2" for="types"    
                                style="display:flex ; flex-direction:column-reverse;">
                                    الاداره الخاصه بالمخالفه</label>
                                <select class="w-100 px-2" name="types[]" id="types" multiple
                                    style="border: 0.2px solid rgb(199, 196, 196);" required>
                                    @foreach (getDepartments() as $department)

                                    <option value="{{ $department->id }}"> {{ $department->name }}</option>
                                    @endforeach

                                </select>

                            </div>
                            {{-- <div class="form-group mb-3">
                                    <div class="select-wrapper">
                                        <div class="select-box d-flex justify-content-between" id="select-box">
                                            <p>الاداره الخاصه بالمخالفه</p>
                                            <i class="fa-solid fa-angle-down" style="color: #A3A1A1;"></i>
                                        </div>
                                        <div class="options" id="options">
                                            <div class="search-box">
                                                <input type="text" id="search-input" placeholder="ابحث هنا ....."
                                                    style="width: 100% !important;">
                                            </div>
                                            @foreach (getDepartments() as $department)
                                                <div class="option">
                                                    <input type="checkbox" id="option{{ $department->id }}"
                            value="{{ $department->id }}" name="types[]">
                            <label for="option{{ $department->id }}"> {{ $department->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
            </div>
            <div id="selected-values" class="mt-2"></div>
        </div> --}}
        <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2" dir="rtl">
            <button type="submit" class="btn-all mx-2 p-2" style="background-color: #274373; color: #ffffff;"
                id="openSecondModalBtn">
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


{{-- <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="lable"> تعديل اسم الأجازه ؟</h5>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body mt-3 mb-5">
                    <form class="edit-grade-form" id="edit-form" action=" {{ route('vacationType.update') }}"
method="POST">
@csrf
<div class="form-group">
    <label for="name">الاسم</label>
    <input type="text" id="nameedit" value="" name="name" class="form-control" required>
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
</div> --}}
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
                <div class="modal-body  d-flex justify-content-center mt-5 mb-5">
                    <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف هذه الاجازه ؟</h5>


                    <input type="text" id="id" value="" hidden name="id" class="form-control">
                </div>
                <div class="modal-footer mx-2 d-flex justify-content-center">
                    <div class="text-end">
                        <button type="button" class="btn-blue" id="closeButton">لا</button>
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
$(document).ready(function() {
    function closeModal() {
        $('#delete').modal('hide');

    }

    $('#closeButton').on('click', function() {
        closeModal();
    });
});
</script>
<script>
function opendelete(id) {
    document.getElementById('id').value = id;
    $('#delete').modal('show');
}

function confirmDelete() {
    var id = document.getElementById('id').value;
    console.log(id);
    var form = document.getElementById('delete-form');

    form.submit();

}

function openedit(id, name, types) {
    document.getElementById('nameedit').value = name;
    document.getElementById('idedit').value = id;

    // Ensure types is an array
    if (typeof types === 'string') {
        try {
            types = JSON.parse(types);
        } catch (e) {
            console.error('Error parsing types:', e);
            types = [];
        }
    }

    console.log('Types:', types); // Debugging

    let select = document.getElementById('types');
    let options = select.options;

    // Clear previous selections
    for (let i = 0; i < options.length; i++) {
        options[i].selected = false;
    }

    // Set new selections
    for (let i = 0; i < options.length; i++) {
        console.log('Option value:', options[i].value); // Debugging
        if (types.includes(parseInt(options[i].value))) {
            options[i].selected = true;
            console.log('Option value:', options[i].value); // Debugging

        }
    }

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

// function confirmAdd() {
//     var name = document.getElementById('nameadd').value;
//     var form = document.getElementById('add-form');

//     form.submit();

// }
function confirmAdd() {
    var name = document.getElementById('nameadd').value;

    var form = document.getElementById('add-form');
    var inputs = form.querySelectorAll('[required]');
    var valid = true;

    inputs.forEach(function(input) {
        if (!input.value) {
            valid = false;
            input.style.borderColor = 'red'; // Optional: highlight empty inputs
        } else {
            input.style.borderColor = ''; // Reset border color if input is filled
        }
    });

    if (valid) {
        form.submit();
    }
}
$(document).ready(function() {


    $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('violations.getAllviolations') }}', // Correct URL concatenation
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


});
</script>

<script>
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
</script>
@endpush