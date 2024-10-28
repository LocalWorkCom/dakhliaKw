@extends('layout.main')

@section('content')
    <main>
        <div class="row " dir="rtl">
            <div class="container  col-11" style="background-color:transparent;">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item "><a href="{{ route('home') }}">الرئيسيه</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">الادارات </a></li>
                        <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل علي الادارة </a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row ">
            <div class="container welcome col-11">
                <p> الأدارات </p>
            </div>
        </div>
        <br>


        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">
                <div class="container col-10 mt-5 mb-5 pb-5 pt-5" style="border:0.5px solid #C7C7CC;">
                    <form action="{{ route('departments.update', $department->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-row mx-md-3 d-flex justify-content-center">

                            <div class="form-group col-md-5 mx-md-2">
                                <label for="mangered">المدير</label>
                                <select name="manger" id="mangered" class=" form-control custom-select custom-select-lg mb-3 select2 "
                                style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;" required>
                                    <option value="">اختر المدير </option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $user->id == old('manger', $department->manger) ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-5 mx-md-2">
                                <label for="name">اسم الادارة </label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $department->name) }}" dir="rtl">
                                @error('name')
                                    <div class="alert alert-danger" style="height: 40px;">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>


                        <div class="form-row mx-md-2 d-flex justify-content-center">
                            <div class="form-group col-md-10">
                                <label for="description">الوصف </label>
                                <input type="text" name="description" class="form-control"
                                    value="{{ old('description', $department->description) }}" dir="rtl">
                                @error('description')
                                    <div class="alert alert-danger" style="height: 40px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row mx-md-2 d-flex justify-content-center">
                            <div class="form-group col-md-10">
                                <label for="employees">الموظفين</label>
                                <select name="employees[]" id="employees" multiple
                                class=" form-control custom-select custom-select-lg mb-3 select2 col-12"
                                style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;">
                                    @foreach ($employee as $employe)
                                    <option value="{{ $employe->id }}"
                                       @if($employe->department_id == $department->id)  selected @endif>
                                        {{ $employe->name }}
                                    </option>
                                @endforeach
                                  </select>
                                {{-- <select name="employees[]" id="employees" class="form-group col-md-12 mx-md-2" multiple
                                    style="height: 150px; font-size: 18px; border:0.2px solid lightgray;" dir="rtl">
                                    @foreach ($employee as $employe)
                                        <option value="{{ $employe->id }}"
                                            {{ $department->employees->contains($employe->id) ? 'selected' : '' }}>
                                            {{ $employe->name }}
                                        </option>
                                    @endforeach
                                </select> --}}
                            </div>
                        </div>
                </div>

                <div class="container col-10 ">
                    <div class="form-row mt-1 mb-3">
                        <button class="btn-blue " type="submit" dir="rtl">
                            <img class="px-1" src="../images/edit.svg" alt="">تعديل

                        </button>
                    </div>
                </div>
                <br><br>
                </form>
            </div>
        </div>
    </main>

    <script>
        $('.select2').select2({
            dir: "rtl"
        });
        // $(document).ready(function() {
        //     var selectedEmployees = @json($employee->toArray());
        //     console.log("selectedEmployeess", selectedEmployees);
        //     var selectedManager = @json($department->manger);
        //     console.log("selectedManager", selectedManager);
        //     // $('#employees').empty();
        //     // Populate employees list, excluding the manager
        //     function populateEmployeesList() {
        //         //    //
        //         $('#employees').empty();
        //         // Populate the employees list, excluding the manager
        //         selectedEmployees.forEach(function(user) {
        //             if (user.id != selectedManager) {
        //                 var isSelected = selectedEmployees.some(function(emp) {
        //                     console.log(emp);
        //                     return emp.id === user.id && user.department_id !== null;
        //                 });
        //                 // Append the option with selected attribute if applicable
        //                 // $('#employees').append('<option value="' + user.id + '"' + (isSelected ? 'selectedd' : '') + '>' + user.name + '</option>');
        //                 $('#employees').append('<option value="' + user.id + '" class="' + (isSelected ?
        //                     'selectedd' : '') + '">' + user.name + '</option>');
        //             }
        //         });
        //     }

        //     // // Initially populate employees list
        //     populateEmployeesList();

        //     // Update employees list when manager is changed
        //     $('#mangered').on('change', function() {
        //         selectedManager = $(this).val();
        //         populateEmployeesList();
        //     });
        // });
    </script>
@endsection
