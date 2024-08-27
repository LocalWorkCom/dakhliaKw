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
تعديل
@endsection
<main>
<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>

                <li class="breadcrumb-item"><a href="{{ route('instant_mission.index') }}">الاوامر</a></li>

                <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل </a></li>
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
<!--  -->

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

                <form action="{{ route('instant_mission.update', $IM->id) }}" method="post" class="text-right"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="container col-10 mt-4 p-4" style="border:0.5px solid #C7C7CC;">
                        <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input2">الاسم</label>
                                <input type="text" id="input2" name="label" class="form-control" placeholder="الاسم"
                                    value="{{ $IM->label }}">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input44"> الموقع</label>
                                <input type="text" id="input44" name="location" class="form-control"
                                    placeholder="الموقع" value="{{ $IM->location }}">
                            </div>

                        </div>

                        <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="group_id">المجموعة</label>
                                <select id="group_id" name="group_id" class="form-control" placeholder="المجموعة">
                                    <option selected disabled>اختار من القائمة</option>
                                    @foreach ($groups as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $IM->group_id == $item->id ? 'selected' : '' }}> {{ $item->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="group_team_id">الفرق</label>
                                <select id="group_team_id" name="group_team_id" class="form-control" placeholder="الفرق">
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
                                    value="{{ $IM->description }}">

                            </div>


                            <input type="hidden" id="remaining-files" name="remaining_files"
                                value="{{ $IM->attachment }}">
                            <div class="form-group col-md-10">
                                <label for="images">اختر الملفات</label>
                                <div class="form-group file-input-container">
                                    <input type="file" name="images[]" id="images" class="form-control" dir="rtl" multiple>
                                    <!-- <span id="file-count1"></span> -->
                                    <span id="file-count"></span>
                                </div>
                                <div class="file-preview1" id="file-preview1" dir="rtl"></div>
                                <!-- Display existing attachments -->
                                <div class="file-preview" id="file-preview">
                                    @foreach (explode(',', $IM->attachment) as $attachment)
                                    <div class="file-item">
                                        <a href="{{ asset($attachment) }}"
                                            target="_blank">{{ basename($attachment) }}</a>
                                        <button type="button" class="btn btn-danger btn-sm delete-file"
                                            data-file="{{ $attachment }}">Delete</button>
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
        const filePreview = document.getElementById('file-preview1');
        const fileCount = document.getElementById('file-count');
        // const remainingFilesInput = document.getElementById('remaining-files');

        fileInput.addEventListener('change', function() {
            filePreview.innerHTML = '';
            const files = Array.from(fileInput.files);
            // fileCount.innerText = `files ${files.length}`;

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