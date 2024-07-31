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
<div class="row ">
    <div class="container welcome col-11">
        @if (url()->current() == url('/users_create/0'))
            <p>المستخدمين</p>
        @elseif (url()->current() == url('/users_create/1'))
            <p>الموظفين</p>
        @endif
        <!-- <p> المستخدمين </p> -->
    </div>
</div>
<div class="row">
    <div class="container  col-11 mt-5 p-0 ">
        <div class="container col-10 mt-5 mb-4 pb-4" style="border:0.5px solid #C7C7CC;">

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


                {{-- {{dd($flag)}} --}}

                <form action="{{ route('user.store') }}" method="post" class="text-right"
                    enctype="multipart/form-data">
                    @csrf

                    @if ($flag == '1')
                        <div class="form-row pt-5 pb-3 d-flex justify-content-around flex-row-reverse"
                            style="background-color:#f5f8fd; border-bottom:0.1px solid lightgray;">
                            <div class="form-group d-flex  justify-content-center col-md-5 mx-2 pb-2">
                                <div class="radio-btns mx-md-4 ">
                                    <input type="radio" class="form-check-input" id="male" name="gender"
                                        value="man" style="height:20px; width:20px;">
                                    <label class="form-check-label mx-2" for="male">ذكر</label>
                                </div>
                                <div class="radio-btns mx-md-4 ">
                                    <input type="radio" class="form-check-input" id="female" name="gender"
                                        value="female" style="height:20px; width:20px;">
                                    <label class="form-check-label mx-2" for="female">انثى</label>
                                </div>
                                <label for="input44 " class="mx-3">الفئة </label>
                            </div>
                            <div class="form-group d-flex  justify-content-end col-md-5 mx-2">
                                <div class="radio-btns mx-md-4 ">
                                    <input type="radio" class="form-check-input" id="solder" name="solderORcivil"
                                        value="military" style="height:20px; width:20px;">
                                    <label class="form-check-label mx-2" for="solder">عسكرى</label>
                                </div>
                                <div class="radio-btns mx-md-4">
                                    <input type="radio" class="form-check-input" id="civil" name="solderORcivil"
                                        value="civil" style="height:20px; width:20px;">
                                    <label class="form-check-label mx-2" for="civil">مدنى</label>
                                </div>
                                <label for="input44" class="mx-3">التصنيف</label>
                            </div>
                        </div>
                    @endif
                    <input type="hidden" name="type" value="{{ $flag }}">
                    <div class="form-row mx-md-2 mt-4 d-flex justify-content-center">
                        @if ($flag == '0')
                            <div class="form-group col-md-10 mx-2">
                                <label for="nameus"> الاسم</label>
                                {{-- <input type="text" id="nameus" name="name" class="form-control" placeholder="الاسم"> --}}
                                <select class="custom-select custom-select-lg mb-3" name="name" id="nameus">
                                    <option selected disabled>اختار من القائمة</option>
                                    @foreach ($alluser as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                @else
                    <div class="form-group col-md-5 mx-2">
                        <label for="job"> الوظيفة</label>
                        <select class="custom-select custom-select-lg mb-3" name="job" id="job">
                            <option selected disabled>اختار من القائمة</option>
                            @foreach ($job as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        {{-- <input type="text" id="job" name="job" class="form-control" required> --}}
                    </div>
                    <div class="form-group col-md-5 mx-2">
                        <label for="nameus"> الاسم</label>
                        <input type="text" id="nameus" name="name" class="form-control" placeholder="الاسم">
                    </div>
                    @endif
            </div>



            @if ($flag == '0')
                <div class="form-row  mx-md-3 d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-5 mx-2">
                        <label for="input3">الباسورد</label>
                        <div class="password-container">
                            <input type="password" id="input3" name="password" class="form-control" placeholder="الباسورد" style="position: absolute">
                            <label class="toggle-password" onclick="togglePasswordVisibility()">
                                <i id="toggleIcon" class="fa fa-eye eye-icon" ></i>
                            </label>
                        </div>
                    </div>


                    <div class="form-group col-md-5 mx-2">
                        <label for="input7"> المهام</label>
                        <select id="input7" name="rule_id" class="form-control" placeholder="المهام">
                            @foreach ($rule as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }}</option>
                            @endforeach


                        </select>
                    </div>
                </div>
            @else
                <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">

                    <div class="form-group col-md-5 mx-2">
                        <label for="input2"> البريد الالكتروني</label>
                        <input type="text" id="input2" name="email" class="form-control"
                            placeholder=" البريد الالكترونى">
                    </div>
                    <div class="form-group col-md-5 mx-2">
                        <label for="input44"> المحافظة</label>
                        <input type="text" id="input44" name="Provinces" class="form-control"
                            placeholder="  المحافظة">
                    </div>

                </div>

                <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-5 mx-2">
                        <label for="input44">العنوان 1</label>
                        <input type="text" id="input44" name="address_1" class="form-control"
                            placeholder="  العنوان">
                    </div>
                    <div class="form-group col-md-5 mx-2">
                        <label for="input44">العنوان 2</label>
                        <input type="text" id="input44" name="address_2" class="form-control"
                            placeholder="  العنوان">
                    </div>
                </div>
                <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-5 mx-2">
                        <label for="input44"> المنطقة</label>
                        <input type="text" id="input44" name="region" class="form-control"
                            placeholder="  المنطقة">
                    </div>
                    <div class="form-group col-md-5 mx-2">
                        <label for="input4"> رقم الهاتف</label>
                        <input type="text" id="input4" name="phone" class="form-control"
                            placeholder=" رقم الهاتف">
                    </div>
                </div>
                <div class="form-row mx-md-3 d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-5 mx-2">
                        <label for="input6">رقم العسكرى</label>
                        <input type="text" id="input6" name="military_number" class="form-control"
                            placeholder="رقم العسكرى">
                    </div>
                    <div class="form-group col-md-5 mx-2">
                        <label for="input66">قطاع </label>
                        <input type="text" id="input66" name="sector" class="form-control"
                            placeholder="قطاع ">
                    </div>
                </div>

                <div class="form-row  mx-md-3 d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-5 mx-2">
                        <label for="input9"> المسمي الوظيفي</label>
                        <input type="text" id="input9" name="job_title" class="form-control"
                            placeholder="المسمي الوظيفي">
                    </div>
                    <div class="form-group col-md-5 mx-2">
                        <label for="input10">الجنسية</label>
                        <input type="text" id="input10" name="nationality" class="form-control"
                            placeholder="الجنسية">
                    </div>
                </div>

                <div class="form-row  mx-md-3 d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-5 mx-2">
                        <label for="input11">رقم المدنى</label>
                        <input type="text" id="input11" name="Civil_number" class="form-control"
                            placeholder="رقم المدنى">
                    </div>
                    <div class="form-group col-md-5 mx-2">
                        <label for="input12">رقم الملف</label>
                        <input type="text" id="input12" name="file_number" class="form-control"
                            placeholder="رقم الملف">
                    </div>
                </div>
                <div class="form-row  mx-md-3 d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-5 mx-2">
                        <label for="input14">الاقدامية</label>
                        <input type="text" id="input14" name="seniority" class="form-control"
                            placeholder="الاقدامية">
                    </div>

                    <div class="form-group col-md-5 mx-2">
                        <label for="input15"> القسم </label>
                        <select id="input15" name="department_id" class="form-control" placeholder="القسم">
                            <option value="{{ null }}" selected>
                                لا يوجد قسم محدد</option>
                            @foreach ($alldepartment as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>

                <div class="form-row mx-md-3  d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-5 mx-2">
                        <label for="input16">موقع العمل</label>
                        <input type="text" id="input16" name="work_location" class="form-control"
                            placeholder="موقع العمل">
                    </div>
                    <div class="form-group col-md-5 mx-2">
                        <label for="input18">المؤهل</label>
                        <input type="text" id="input18" name="qualification" class="form-control"
                            placeholder="المؤهل">
                    </div>
                </div>

            <div class="form-row mx-md-3  d-flex justify-content-center flex-row-reverse">
                <div class="form-group col-md-5 mx-2">
                    <label for="input19">تاريخ الميلاد</label>
                    <input type="date" id="input19" name="date_of_birth" class="form-control"
                        placeholder="تاريخ الميلاد">
                </div>
                <div class="form-group col-md-5 mx-2">
                    <label for="input20">تاريخ الالتحاق</label>
                    <input type="date" id="input20" name="joining_date" class="form-control"
                        placeholder="تاريخ الالتحاق">
                </div>
            </div>
            <div class="form-row mx-md-2  d-flex justify-content-center flex-row-reverse">
                {{-- <div class="form-group col-md-5 mx-2">
                    <label for="input22">مدة الخدمة</label>
                    <input type="number" id="input22" name="end_of_service" class="form-control"
                        placeholder="مدة الخدمة ">
                </div> --}}
                <div class="form-group col-md-10 mx-2">
                    <label for="input24"> الرتبة</label>
                    <select id="input24" name="grade_id" class="form-control" placeholder="الرتبة">
                        @foreach ($grade as $item)

                        <option value="{{ $item->id }}" {{ $item->name == "عسكرى" ? 'selected':'' }}> {{ $item->name }}
                        </option>
                        @endforeach
                    </select>

                </div>  </div>

                <div class="form-row mx-md-2  d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-10">
                        <label for="input5"> الوصف</label>
                        <textarea type="text" id="input5" name="description" class="form-control" placeholder="الوصف"
                            rows="3"></textarea>
                    </div>
                </div>

                <div class="form-row mx-md-2  d-flex justify-content-center flex-row-reverse">
                    <div class="form-group col-md-10">
                        <label for="input23">الصورة</label>
                        <input type="file" class="form-control" name="image" id="input23"
                            placeholder="الصورة">
                    </div>
                </div>
            @endif



            {{-- <div class="form-row mx-3 d-flex justify-content-center">
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


    @if ($flag == '0')
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
            <input type="password" id="password" name="password" class="form-control">

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
                    <option value=""></option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-5 mx-2">
                <label for="military_number">رقم العسكرى</label>
                <input type="text" id="military_number" name="military_number" class="form-control">
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
        <div class="form-group col-md-5 ">
            <label for="description">وصف</label>
            <textarea class="form-control" id="description" name="description" placeholder="الوصف" rows="3"></textarea>
            <input type="file" id="image" name="image" class="form-control" required>

        </div>
        <div class="form-group col-md-5 mx-2">
            <label for="phone">رقم الهاتف</label>
            <input type="text" id="phone" name="phone" class="form-control">
        </div>
    </div>


    @endif --}}



</div>



    <div class="container col-10 mt-3 mb-3 ">
        <div class="form-row col-10 " dir="ltr">
            <button class="btn-blue " type="submit">
                اضافة </button>
        </div>
    </div>
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

<script>
    function togglePasswordVisibility() {
        var passwordInput = document.getElementById('input3');
        var toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    </script>
@endsection
