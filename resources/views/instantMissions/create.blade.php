@extends('layout.main')


@section('content')
    <style>
        .file-preview {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .file-item {
            position: relative;
            margin: 5px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 200px;
        }

        .file-item img {
            max-width: 100%;
            max-height: 50px;
            margin-right: 10px;
        }

        .file-item button {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #dc3545;
            border: none;
            color: white;
            padding: 2px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
@section('title')
    اضافة
@endsection

<div class="row " dir="rtl">
    <div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>

                <li class="breadcrumb-item"><a href="{{ route('instant_mission.index') }}">الاوامر</a></li>

                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافه </a></li>
            </ol>

        </nav>
    </div>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> الاوامر </p>
    </div>
</div>
<br>



<div class="row">
    <div class="container  col-11 mt-3 p-0  pt-5 pb-4">


        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="">

            <form action="{{ route('instant_mission.store') }}" method="post" class="text-right"
                enctype="multipart/form-data">
                @csrf

                <div class="container col-10 mt-4 p-4" style="border:0.5px solid #C7C7CC;">
                    <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">
                        <div class="form-group col-md-5 mx-2">
                            <label for="input22">التاريخ</label>
                            <input type="date" id="input2"2 name="date" class="form-control"
                                placeholder="التاريخ"  value="{{ old('date') }}" required>
                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="input2">اسم أمر الخدمة</label>
                            <input type="text" id="input2" name="label" class="form-control" placeholder="الاسم"
                                value="{{ old('label') }}" required>
                        </div>


                    </div>

                    <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">
                        <div class="form-group col-md-5 mx-2">
                            <label for="group_id">المجموعة</label>
                            <select id="group_id" name="group_id" class="form-control select2">
                                <option selected disabled>اختار من القائمة</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger span-error" id="group_id-error"></span>

                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="group_team_id">الفرق</label>
                            <select id="group_team_id" name="group_team_id" class="form-control select2">
                                <option selected disabled>اختار من القائمة</option>
                                @foreach($groupTeams as $team)
                                    <option value="{{ $team->id }}" {{ old('group_team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-5 mx-2">
                            <label for="inspectors">المفتش</label>
                            <select id="inspectors" name="inspectors" class="form-control select2">
                                <option selected >اختار الكل </option>
                                @foreach($inspectors as $inspector)
                                    <option value="{{ $inspector->id }}" {{ old('inspectors') == $inspector->id ? 'selected' : '' }}>
                                        {{ $inspector->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group col-md-5 mx-md-2">
                            <label for="input44"> الموقع</label>
                            <input type="text" id="input44" name="location" class="form-control"
                                placeholder="الموقع" value="{{ old('location') }}" required>

                        </div>
                        <div class="form-group col-md-10 mx-2">
                            <label for="description">الملاحظات </label>
                            <input type="text" name="description" class="form-control"
                                value="{{ old('description') }}">
                            {{-- @error('description')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror --}}
                        </div>
                        <div class="form-group col-md-10">
                            <label for="images"> اختار الملفات</label>
                            <div class="form-group file-input-container">
                                <input type="file" name="images[]" id="images" class="form-control" dir="rtl"
                                    multiple>
                                <span id="file-count"></span>
                            </div>
                            <div class="file-preview" id="file-preview" dir="rtl"></div>
                        </div>

                    </div>
                </div>


                <div class="container col-10 ">
                    <div class="form-row mt-5 mb-2">
                        <button type="submit" class="btn-blue">حفظ</button>
                    </div>
                </div>

                <br>
            </form>

        </div>

    </div>

</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


<script>
    $('.select2').select2({
        dir: "rtl"
    });
    // const fileInput = document.getElementById('images');
    // const filePreview = document.getElementById('file-preview');
    // const fileCount = document.getElementById('file-count');

    // fileInput.addEventListener('change', function() {
    //     filePreview.innerHTML = '';
    //     const files = Array.from(fileInput.files);
    //     fileCount.innerText = `files ${files.length}`;

    //     files.forEach((file, index) => {
    //         const fileItem = document.createElement('div');
    //         fileItem.classList.add('file-item');

    //         const fileName = document.createElement('span');
    //         fileName.textContent = file.name;

    //         const deleteButton = document.createElement(
    //             'button');
    //         deleteButton.textContent = 'Delete';
    //         deleteButton.addEventListener('click', () => {
    //             files.splice(index, 1);
    //             fileInput.value = '';
    //             fileCount.innerText =
    //                 `files ${files.length}`;
    //             fileItem.remove();
    //         });

    //         fileItem.appendChild(fileName);
    //         fileItem.appendChild(deleteButton);
    //         filePreview.appendChild(fileItem);
    //     });
    // });
</script>

<script>
    $('.select2').select2({
        // dir: "rtl"
    });
    // Handle change in group selection
    $('#group_id').change(function() {
        var group_id = $(this).val();

        if (group_id) {
            // Clear and reset the inspectors select box when group is changed
            $('#inspectors').empty().append('<option selected disabled>اختار من القائمة</option>').prop(
                'disabled', false);

            // Fetch and populate group teams
            $.ajax({
                url: '/getGroups/' + group_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#group_team_id').empty();
                    $('#group_team_id').append(
                        '<option selected disabled>اختار من القائمة</option>');

                    // Populate the group teams select box
                    $.each(data, function(key, employee) {
                        $('#group_team_id').append('<option value="' + employee.id + '">' +
                            employee.name + '</option>');
                    });

                    // Trigger change on group_team_id to clear inspectors on new group selection
                    $('#group_team_id').trigger('change');
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr.responseText);
                }
            });
        } else {
            // If no group is selected, clear both team and inspector select boxes
            $('#group_team_id').empty().append('<option selected disabled>اختار من القائمة</option>').prop(
                'disabled', true);
            $('#inspectors').empty().append('<option selected disabled>اختار من القائمة</option>').prop(
                'disabled', true);
        }
    });

    // Handle change in group team selection
    $('#group_team_id').change(function() {
        var group_team_id = $(this).val();
        var group_id = $('#group_id').val();

        if (group_id && group_team_id) {
            // Fetch and populate inspectors based on selected group and team
            $.ajax({
                url: '/getInspector/' + group_team_id + '/' + group_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#inspectors').empty();
                    $('#inspectors').append('<option selected disabled>اختار من القائمة</option>');

                    // Populate the inspectors select box
                    $.each(data, function(key, employee) {
                        $('#inspectors').append('<option value="' + employee.id + '">' +
                            employee.name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr.responseText);
                }
            });
        } else {
            // If no team is selected, clear the inspectors select box
            $('#inspectors').empty().append('<option selected disabled>اختار من القائمة</option>').prop(
                'disabled', true);
        }
    });

    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();

        // Capture the old selected values from the Blade template
        var oldGroupId = "{{ old('group_id') }}";
        var oldGroupTeamId = "{{ old('group_team_id') }}";
        var oldInspectors = @json(old('inspectors', [])); // Convert old inspectors array to JavaScript array
        var oldDate = "{{ old('date_field') }}"; // Capture the old date value

        // Handle change in group selection
        $('#group_id').change(function() {
            var group_id = $(this).val();

            if (group_id) {
                // Clear and reset inspectors select box when group is changed
                $('#inspectors').empty().append('<option selected disabled>اختار من القائمة</option>')
                    .prop('disabled', false);

                // Fetch and populate group teams
                $.ajax({
                    url: '/getGroups/' + group_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#group_team_id').empty().append(
                            '<option selected disabled>اختار من القائمة</option>');
                        $.each(data, function(key, team) {
                            var isSelected = (team.id == oldGroupTeamId) ?
                                'selected' : '';
                            $('#group_team_id').append('<option value="' + team.id +
                                '" ' + isSelected + '>' + team.name +
                                '</option>');
                        });

                        // Trigger the change event to fetch inspectors for the old group team
                        $('#group_team_id').trigger('change');
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        console.log('XHR:', xhr.responseText);
                    }
                });
            } else {
                $('#group_team_id').empty().append(
                    '<option selected disabled>اختار من القائمة</option>').prop('disabled', true);
                $('#inspectors').empty().append('<option selected disabled>اختار من القائمة</option>')
                    .prop('disabled', true);
            }
        });

        // Trigger the change on group_id to preselect the old values
        if (oldGroupId) {
            $('#group_id').val(oldGroupId).trigger('change');
        }

        // Handle change in group team selection
        $('#group_team_id').change(function() {
            var group_team_id = $(this).val();
            var group_id = $('#group_id').val();

            if (group_id && group_team_id) {
                // Fetch and populate inspectors
                $.ajax({
                    url: '/getInspector/' + group_team_id + '/' + group_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#inspectors').empty().append(
                            '<option selected disabled>اختار من القائمة</option>');
                        $.each(data, function(key, inspector) {
                            var isSelected = (oldInspectors.includes(inspector.id
                                .toString())) ? 'selected' : '';
                            $('#inspectors').append('<option value="' + inspector
                                .id + '" ' + isSelected + '>' + inspector.name +
                                '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        console.log('XHR:', xhr.responseText);
                    }
                });
            } else {
                $('#inspectors').empty().append('<option selected disabled>اختار من القائمة</option>')
                    .prop('disabled', true);
            }
        });

        // Trigger the change event to initialize with old group_team_id and inspectors
        if (oldGroupTeamId) {
            $('#group_team_id').val(oldGroupTeamId).trigger('change');
        }
        if (oldDate) {
        $('#date').val(oldDate);
    }
    });


    // $(document).ready(function() {
    // $('#group_id').on('change', function() {
    //     var group_id = $(this).val();


    //     if (group_id) {
    //         $.ajax({
    //             url: '/getGroups/' + group_id,
    //             type: 'GET',
    //             dataType: 'json',
    //             success: function(data) {
    //                 $('#group_team_id').empty();
    //                 $('#group_team_id').append(
    //                     '<option selected disabled> اختار من القائمة </option>'
    //                 );
    //                 $.each(data, function(key,
    //                     employee) {
    //                     console.log(
    //                         employee
    //                     );
    //                     $('#group_team_id')
    //                         .append(
    //                             '<option value="' +
    //                             employee
    //                             .id +
    //                             '">' +
    //                             employee
    //                             .name +
    //                             '</option>'
    //                         );
    //                 });
    //             },
    //             error: function(xhr, status,
    //                 error) {
    //                 console.log('Error:',
    //                     error);
    //                 console.log('XHR:', xhr
    //                     .responseText);
    //             }
    //         });
    //     } else {
    //         $('#group_team_id').empty();
    //         $('#inspectors').empty();

    //     }
    // });
    // });

    // $(document).ready(function() {
    // $('#group_team_id').on('change', function() {
    //     var group_team_id = $(this).val();
    //     var group_id = $('#group_id').val();
    //     console.log(group_team_id);


    //     if (group_id) {
    //         $.ajax({
    //             url: '/getInspector/' +
    //                 group_team_id + '/' + group_id,
    //             type: 'GET',
    //             dataType: 'json',
    //             success: function(data) {
    //                 $('#inspectors').empty();
    //                 $('#inspectors').append(
    //                     '<option selected disabled> اختار من القائمة </option>'
    //                 );
    //                 $.each(data, function(key,
    //                     employee) {
    //                     // console.log(employee);
    //                     $('#inspectors')
    //                         .append(
    //                             '<option value="' +
    //                             employee
    //                             .id +
    //                             '">' +
    //                             employee
    //                             .name +
    //                             '</option>'
    //                         );
    //                 });
    //             },
    //             error: function(xhr, status,
    //                 error) {
    //                 console.log('Error:',
    //                     error);
    //                 console.log('XHR:', xhr
    //                     .responseText);
    //             }
    //         });
    //     } else {
    //         $('#inspectors').empty();
    //     }
    // });
    // });
</script>


@endsection
