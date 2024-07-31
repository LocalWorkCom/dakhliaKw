@extends('layout.main')

@section('content')

<main>
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="{{ route('home') }}">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">القطاعات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل علي الادارة </a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> القطاعات </p>
        </div>
    </div>
    <br>


    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="container col-10 mt-5 mb-5 pb-5 pt-5" style="border:0.5px solid #C7C7CC;">
                <form action="{{ route('departments.update', $department->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-row mx-md-3 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="name">اسم الادارة </label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}">
                            @error('name')
                            <div class="alert alert-danger" style="height: 40px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-5 mx-md-2">
                            <label for="mangered">المدير</label>
                            <select name="manger" id="mangered" class="form-control">
                                <option value="">اختر المدير </option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == old('manger', $department->manger) ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    

                    <div class="form-row mx-md-2 d-flex justify-content-center">
                    <div class="form-group col-md-10">
                        <label for="description">الوصف </label>
                        <input type="text" name="description" class="form-control"  value="{{ old('description', $department->description) }}">
                        @error('description')
                            <div class="alert alert-danger" style="height: 40px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row mx-md-2 d-flex justify-content-center">
                    <div class="form-group col-md-10">
                        <label for="employees">الموظفين </label>
                        <select name="employess[]" id="employees" class="form-group col-md-12 mx-md-2" multiple style="height: 150px; font-size: 18px; border:0.2px solid lightgray;" dir="rtl">
                        @foreach($employee as $employe)
                <option value="{{ $employe->id }}" {{ $department->employees->contains($employe->id) ? 'selected' : '' }}>
                    {{ $employe->name }}
                </option>
            @endforeach
                        </select>
                        
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
    <!-- **********modal******** -->
   
</main>

<!-- <div class="container">
    <h1>Edit Department</h1>
    <form action="{{ route('departments.update', $department->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name </label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}">
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="manger">Manager</label>
            <select name="manger" class="form-control">
                <option value="">Select Manager</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == old('manger', $department->manger) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            @error('manger')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="manger_assistance">Manager Assistant</label>
            <select name="manger_assistance" class="form-control">
                <option value="">Select Manager Assistant</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == old('manger_assistance', $department->manger_assistance) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            @error('manger_assistance')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="description">Description </label>
            <input type="text" name="description" class="form-control"  value="{{ old('name', $department->description) }}">
            @error('description')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div> -->
<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script> -->

<!-- <script>
$(document).ready(function() {
    $('#employees').select2({
        placeholder: 'Select employees',
        width: 'resolve'
    });
});
</script> -->
<script>
    $(document).ready(function() {
        var selectedEmployees = @json($employee->toArray());
        console.log("selectedEmployeess",selectedEmployees);
        var selectedManager = @json($department->manger);
        console.log("selectedManager" ,selectedManager);
        // $('#employees').empty();
        // Populate employees list, excluding the manager
        function populateEmployeesList() {
        //    // 
        $('#employees').empty();
         // Populate the employees list, excluding the manager
         selectedEmployees.forEach(function(user) {
                if (user.id != selectedManager) {
                    var isSelected = selectedEmployees.some(function(emp) { 
                        console.log(emp);
                        return emp.id === user.id && user.department_id !== null ; });
                    // Append the option with selected attribute if applicable
                    // $('#employees').append('<option value="' + user.id + '"' + (isSelected ? 'selectedd' : '') + '>' + user.name + '</option>');
                    $('#employees').append('<option value="' + user.id + '" class="' + (isSelected ? 'selectedd' : '') + '">' + user.name + '</option>');
                }
            });
        }

        // // Initially populate employees list
        populateEmployeesList();

        // Update employees list when manager is changed
        $('#mangered').on('change', function() {
            selectedManager = $(this).val();
            populateEmployeesList();
        });
    });

</script>

<style>
    .selectedd {
    background-color: green; /* Change to desired color */
    color: white; /* Optional: Change text color for better contrast */
}
</style>
@endsection
