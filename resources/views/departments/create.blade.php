@extends('layout.main')

@section('content')

<main>
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="{{ route('home') }}">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">الادارات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('departments.create') }}">
                        اضافة اداره</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> الــــــــــادارات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
        <div class="container col-10 mt-5 mb-5 pb-5" style="border:0.5px solid #C7C7CC;">
            <form action="{{ route('departments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row mx-2 mt-4 d-flex justify-content-center">
                    <div class="form-group col-md-5 mx-2">
                        <label for="name">اسم الادارة </label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-5 mx-2">
                        <label for="manger">المدير</label>
                        <select name="manger" class="form-control">
                            <option value="">اختار المدير</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('manger')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                    </div>
                </div>
                <div class="form-row mx-2 d-flex justify-content-center">
                    <!--<div class="form-group col-md-5 mx-2">-->
                    <!--    <label for="manger_assistance"> مساعد المدير</label>-->
                    <!--    <select name="manger_assistance" class="form-control">-->
                    <!--        <option value="">اختار مساعد المدير</option>-->
                    <!--        @foreach($users as $user)-->
                    <!--        <option value="{{ $user->id }}">{{ $user->name }}</option>-->
                    <!--        @endforeach-->
                    <!--    </select>-->
                    <!--    @error('manger_assistance')-->
                    <!--    <div class="alert alert-danger">{{ $message }}</div>-->
                    <!--    @enderror-->
                    <!--</div>-->

                    <div class="form-group col-md-5 mx-2">
                        <label for="description">الوصف </label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                        @error('description')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                </div>
                <div class="container col-10 mt-5 mb-5 " >
                <div class="form-row col-10 " dir="ltr">
                    <button class="btn-blue " type="submit">
                        اضافة </button>
                </div>   </div>
                <br>

            </form>
        </div>



    </div>



    </div>
</main>

<!-- 
<div class="container">
    <h1>Create Department</h1>
    <form action="{{ route('departments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="name">Name </label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="manger">Manager</label>
            <select name="manger" class="form-control">
                <option value="">Select Manager</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @error('manager')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            
        </div>
        <div class="form-group">
            <label for="manger_assistance">Manager Assistant</label>
            <select name="manger_assistance" class="form-control">
                <option value="">Select Manager Assistant</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @error('manger_assistance')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="description">Description </label>
            <input type="text" name="description" class="form-control" value="{{ old('description') }}">
            @error('description')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <h1>Departments</h1>
    <ul>
        @foreach($departments as $department)
            <li>
                {{ $department->name }} (Parent: {{ $department->parent ? $department->parent->name : 'None' }})
                @if($department->children->count())
                    <ul>
                        @foreach($department->children as $child)
                            <li>{{ $child->name }}</li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div> -->
@endsection