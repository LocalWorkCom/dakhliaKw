@extends('layout.main')


@section('content')
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
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
عرض
@endsection

<main>
<div class="row col-11" dir="rtl">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>

            <li class="breadcrumb-item"><a href="{{ route('instant_mission.index') }}">الاوامر</a></li>

            <li class="breadcrumb-item active" aria-current="page"> <a href=""> عرض </a></li>
        </ol>

    </nav>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> الاوامر </p>
    </div>
</div>
<br>



<div class="row">
    <div class="container  col-11 mt-3 p-0  pt-5 pb-4">


        <div class="">

                <div class="container col-10 mt-4 p-4" style="border:0.5px solid #C7C7CC;">
                    <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">
                        <div class="form-group col-md-5 mx-2">
                            <label for="input2">الاسم</label>
                            <input type="text" id="input2" name="label" class="form-control" placeholder="الاسم"
                                value="{{ $IM->label }}" disabled>
                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="input44"> الموقع</label>
                            <input type="text" id="input44" name="location" class="form-control"
                                placeholder="الموقع" value="{{ $IM->location }}" disabled>
                        </div>

                    </div>

                    <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">
                        <div class="form-group col-md-5 mx-2">
                            <label for="group_id">المجموعة</label>
                            <select id="group_id" name="group_id" class="form-control" placeholder="المجموعة" disabled>
                                <option  disabled>اختار من القائمة</option>
                                @foreach ($groups as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $IM->group_id == $item->id ? 'selected' : '' }}> {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="group_team_id">الفرق</label>
                            <select id="group_team_id" name="group_team_id" class="form-control" placeholder="الفرق" disabled>
                                <option selected disabled>اختار من القائمة</option>
                                @foreach ($groupTeams as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $IM->group_team_id == $item->id ? 'selected' : '' }}> {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-10 mx-md-2">
                            <label for="description">الوصف </label>
                            <input type="text" name="description" class="form-control"
                                value="{{ $IM->description }}" disabled>
                            {{-- @error('description')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror --}}
                        </div>

                        {{-- <div class="form-group col-md-10">
                            <label for="images"> اختار الملفات</label>
                            <div class="form-group file-input-container">
                                <input type="file" name="images[]" id="images" class="form-control" multiple>
                                <span id="file-count"></span>
                            </div>
                            <div class="file-preview" id="file-preview"></div>
                        </div> --}}
                        {{-- {{ dd( $IM->attachment) }} --}}
              
                        <div class="form-group col-md-10">
                            <label for="images">اختر الملفات</label>
                            <div class="form-group file-input-container">
                                <input type="file" name="images[]" id="images" class="form-control" multiple
                                    value="{{ $IM->attachment }}" dir="rtl" disabled>
                                <span id="file-count"></span>
                            </div>

                            <!-- Display existing attachments -->
                            <div class="file-preview" id="file-preview" dir="rtl">
                                @foreach (explode(',', $IM->attachment) as $attachment)
                                    <div class="file-item">
                                        <a href="{{ asset($attachment) }}"
                                            target="_blank">{{ basename($attachment) }}</a> 
                                    </div>
                                @endforeach
                            </div>
                    </div>

                </div>
        </div>


        <div class="container col-10 ">
            <div class="form-row mt-5 mb-2">
                <button type="submit" class="btn-blue">تعديل</button>
            </div>
        </div>

        <br>
        </form>

    </div>

</div>

</div>


</main>
{{-- <script>

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('images');
        const filePreview = document.getElementById('file-preview');
        const fileCount = document.getElementById('file-count');
        const remainingFilesInput = document.getElementById('remaining-files');

        // Function to update file preview and remaining files input
        function updateFilePreview() {
            filePreview.innerHTML = remainingFilesInput.value;
            const files = Array.from(fileInput.files);
            fileCount.innerText = `files ${files.length}`;

            files.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.classList.add('file-item');

                const fileName = document.createElement('span');
                fileName.textContent = file.name;

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.addEventListener('click', () => {
                    files.splice(index, 1); // Remove file from array
                    const dataTransfer = new DataTransfer();
                    files.forEach(f => dataTransfer.items.add(f));
                    fileInput.files = dataTransfer.files;
                    updateFilePreview(); // Update preview
                });

                fileItem.appendChild(fileName);
                fileItem.appendChild(deleteButton);
                filePreview.appendChild(fileItem);
            });

            // Update remaining files hidden input
            const fileNames = files.map(file => file.name).join(',');
            remainingFilesInput.value = fileNames;
        }

        // Handle file input change event
        fileInput.addEventListener('change', function() {
            updateFilePreview();
        });

        // Handle existing files removal from preview
        filePreview.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-file')) {
                const fileItem = e.target.closest('.file-item');
                const fileName = e.target.getAttribute('data-file');

                const remainingFiles = remainingFilesInput.value.split(',').filter(file => file !== fileName).join(',');
                remainingFilesInput.value = remainingFiles;

                fileItem.remove();
            }
        });

        // Initialize preview with existing files
        (function initializeFilePreview() {
            const existingFiles = remainingFilesInput.value.split(',');
            existingFiles.forEach(fileName => {
                if (fileName) {
                    const fileItem = document.createElement('div');
                    fileItem.classList.add('file-item');

                    const fileNameSpan = document.createElement('span');
                    fileNameSpan.textContent = fileName;

                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Delete';
                    deleteButton.classList.add('delete-file');
                    deleteButton.setAttribute('data-file', fileName);
                    
                    fileItem.appendChild(fileNameSpan);
                    fileItem.appendChild(deleteButton);
                    filePreview.appendChild(fileItem);
                }
            });
        })();
    });

</script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filePreview = document.getElementById('file-preview');
        const remainingFilesInput = document.getElementById('remaining-files');

        // console.log(remainingFilesInput.value);
        filePreview.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-file')) {
                const fileItem = e.target.closest('.file-item');
                const fileName = e.target.getAttribute('data-file');

                console.log('File to be removed:', fileName);
                console.log('Current files:', remainingFilesInput.value);

                const remainingFiles = remainingFilesInput.value.split(',').filter(file => {
                    console.log('Comparing:', file, ',', fileName);
                    return file !== fileName;
                }).join(',');

                // console.log( remainingFiles);
                remainingFilesInput.value = remainingFiles;
                // Remove the file item from the UI
                fileItem.remove();

                // console.log(document.getElementById('file-preview'));
            }
        });
    });
</script> 

<script>
    const fileInput = document.getElementById('images');
    const filePreview = document.getElementById('file-preview');
    const fileCount = document.getElementById('file-count');
    const remainingFilesInput = document.getElementById('remaining-files');

    fileInput.addEventListener('change', function() {
        filePreview.innerHTML = remainingFilesInput;
        const files = Array.from(fileInput.files);
        fileCount.innerText = `files ${files.length}`;

        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.classList.add('file-item');

            const fileName = document.createElement('span');
            fileName.textContent = file.name;

            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Delete';
            deleteButton.addEventListener('click', () => {
                files.splice(index, 1);
                fileInput.value = '';
                fileCount.innerText = `files ${files.length}`;
                fileItem.remove();
            });

            fileItem.appendChild(fileName);
            fileItem.appendChild(deleteButton);
            filePreview.appendChild(fileItem);
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#group_id').on('change', function() {
            var group_id = $(this).val();


            if (group_id) {
                $.ajax({
                    url: '/getGroups/' + group_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#group_team_id').empty();
                        $('#group_team_id').append(
                            '<option selected disabled> اختار من القائمة </option>');
                        $.each(data, function(key, employee) {
                            console.log(employee);
                            $('#group_team_id').append('<option value="' + employee
                                .id + '">' + employee.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        console.log('XHR:', xhr.responseText);
                    }
                });
            } else {
                $('#group_team_id').empty();
            }
        });
    });
</script>


@endsection
