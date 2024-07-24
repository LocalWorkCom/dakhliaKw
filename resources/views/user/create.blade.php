@extends('layout.main')
@section('content')
@section('title')
    اضافة
@endsection
<div class="row col-11" dir="rtl">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>

                @if (url()->current() == url('/users_create/0'))
                <li class="breadcrumb-item"><a href="{{ route('user.index', 0) }}">المستخدمين</a></li>

                @elseif (url()->current() == url('/users_create/1'))
                <li class="breadcrumb-item"><a href="{{ route('user.employees', 1) }}">الموظفين</a></li>

                @endif
            <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة </a></li>
        </ol>
    </nav>
</div>
<div class="row">
    <div class="container  col-11 mt-3 p-0 ">
        <div class="container col-10 mt-5 mb-5 pb-5" style="border:0.5px solid #C7C7CC;">

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
            <div class="p-5">


                {{-- {{dd($flag)}} --}}

                <form action="{{ route('user.store') }}" method="post" class="text-right" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="type" value="{{ $flag }}">
                    <div class="form-row mx-3 mt-4 d-flex justify-content-center">
                      
                        <div class="form-group col-md-5 mx-2 ">
                            <label for="job"> الوظيفة</label>
                            <select class="custom-select custom-select-lg mb-3" name="job" id="job">
                                <option selected disabled>Open this select menu</option>
                                @foreach ($job as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" id="job" name="job" class="form-control" required> --}}
                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="nameus"> الاسم</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row mx-3 d-flex justify-content-center">
                        @if ($flag == "0")
                            <div class="form-group col-md-5 mx-2" >
                                <label for="military_number">رقم العسكرى</label>
                                <input type="text" id="military_number" name="military_number" class="form-control"
                                    >
                            </div>
                        @endif
                        

                        <div class="form-group col-md-5 mx-2">
                            <label for="phone">رقم المحمول</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>

                    </div>
                   
                   
          
            <div class="form-row mx-3 d-flex justify-content-center">
                <div class="form-group col-md-5 mx-2">
                    <label for="filenum">رقم الملف</label>
                    <input type="text" id="filenum" name="file_number" class="form-control">
                </div>
                <div class="form-group col-md-5 mx-2">
                    <label for="department">الادارة</label>
                    <select class="custom-select custom-select-lg mb-3" name="department" id="department">
                        <option selected disabled>Open this select menu</option>
                        @foreach ($alldepartment as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            

            @if ($flag == "0")
            <div class="form-row mx-3 d-flex justify-content-center">
                <div class="form-group col-md-5 mx-2">
                    <label for="rule_id">المهام</label>
                    <select class="custom-select custom-select-lg mb-3" name="rule" id="rule_id">
                        <option selected disabled>Open this select menu</option>
                        @foreach ($rule as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-5 mx-2">
                    <label for="Civil_number">الباسورد</label>
                    <input type="text" id="password" name="password" class="form-control" >

                </div>
            </div>
            @else
            <div class="form-row mx-2 d-flex justify-content-center">
                <div class="form-group col-md-10 ">
                    <input type="checkbox" class="form-check-input " id="myCheckbox" name="solder"
                    style="height:20px; width:20px;">
                    <label class="form-check-label mx-2" for="myCheckbox">عسكرى</label>
                </div>
            </div>
            <div id="grade" style="display: none;">
                <div class="form-row mx-2 d-flex justify-content-center">
                   
                        <div class="form-group col-md-5 ">
                            <label for="grade_id">الرتبة</label>
                            <select class="custom-select custom-select-lg mb-3" name="grade_id" id="grade_id">
                                <option selected disabled>Open this select menu</option>
                                @foreach ($grade as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                {{-- <option value=""></option> --}}
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-5 mx-2" >
                            <label for="military_number">رقم العسكرى</label>
                            <input type="text" id="military_number" name="military_number" class="form-control"
                                >
                        </div>
                    </div>
                
            </div>
            <div class="form-row mx-3 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-2">
                            <label for="date_of_birth">تاريخ الميلاد</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control">

                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="image">الصورة</label>
                            <input type="file" id="image" name="image" class="form-control">

                        </div>
                    </div>
                    <div class="form-row mx-2 d-flex justify-content-center">
                        <div class="form-group col-md-10 ">
                            <label for="description">وصف</label>
                            <textarea class="form-control" id="description" name="description" placeholder="الوصف"
                                rows="3"></textarea>
                            {{-- <input type="file" id="image" name="image" class="form-control" required> --}}

                        </div>
                    </div>

            @endif



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

</div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const checkbox = document.getElementById("myCheckbox");
    const grade = document.getElementById("grade");

    checkbox.addEventListener("change", function() {
        if (checkbox.checked) {
            grade.style.display = "block";
        } else {
            grade.style.display = "none";
        }

    });
});
</script>


@endsection