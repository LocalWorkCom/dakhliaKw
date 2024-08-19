@extends('layout.main')
@section('title')
    تعديل
@endsection
@section('content')


    <section>
        <div class="row col-11" dir="rtl">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>

                    @if ($user->flag == 'user')
                        <li class="breadcrumb-item"><a href="{{ route('user.index', 0) }}">المستخدمين</a></li>
                    @elseif ($user->flag == 'employee')
                        <li class="breadcrumb-item"><a href="{{ route('user.employees', 1) }}">الموظفين</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل </a></li>
                </ol>

            </nav>
        </div>
        <div class="row ">
            <div class="container welcome col-11">
                @if ($user->flag == 'user')
                    <p>المستخدمين</p>
                @elseif ($user->flag == 'employee')
                    <p>الموظفين</p>
                @endif
            </div>
        </div>


        </div>

        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">
                <div class="container col-10 mt-1 mb-5 pb-5  mt-5" style="border:0.5px solid #C7C7CC;">

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
                    {{-- {{ dd($user) }} --}}

                    <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-row pt-5 pb-3 d-flex justify-content-around flex-row-reverse"
                            style="background-color:#f5f8fd; border-bottom:0.1px solid lightgray;">
                            <div class="form-group d-flex  justify-content-center col-md-5 mx-2 pb-2">
                                {{-- {{ dd($user->type) }} --}}
                                @if ($user->type == 'man')
                                    <div class="radio-btns mx-md-4 ">
                                        <input type="radio" class="form-check-input" id="male" name="gender"
                                            value="man" style="height:20px; width:20px;" checked>
                                        <label class="form-check-label mx-2" for="male">ذكر</label>
                                    </div>
                                    <div class="radio-btns mx-md-4 ">
                                        <input type="radio" class="form-check-input" id="female" name="gender"
                                            value="female" style="height:20px; width:20px;">
                                        <label class="form-check-label mx-2" for="female">انثى</label>
                                    </div>
                                @else
                                    <div class="radio-btns mx-md-4 ">
                                        <input type="radio" class="form-check-input" id="male" name="gender"
                                            value="man" style="height:20px; width:20px;">
                                        <label class="form-check-label mx-2" for="male">ذكر</label>
                                    </div>
                                    <div class="radio-btns mx-md-4 ">
                                        <input type="radio" class="form-check-input" id="female" name="gender"
                                            value="female" style="height:20px; width:20px;" checked>
                                        <label class="form-check-label mx-2" for="female">انثى</label>
                                    </div>
                                @endif
                                <label for="input44">الفئة</label>
                            </div>
                            <div class="form-group d-flex  justify-content-center col-md-5 mx-2 pb-2">

                                @if ($user->employee_type == 'civil')
                                    <div class="radio-btns mx-md-4 ">
                                        <input type="radio" class="form-check-input" id="solder" name="solderORcivil"
                                            value="military" style="height:20px; width:20px;">
                                        <label class="form-check-label mx-2" for="solder">عسكرى</label>
                                    </div>
                                    <div class="radio-btns mx-md-4 ">
                                        <input type="radio" class="form-check-input" id="civil" name="solderORcivil"
                                            value="civil" style="height:20px; width:20px;" checked>
                                        <label class="form-check-label mx-2" for="civil">مدنى</label>
                                    </div>
                                @else
                                    <div class="radio-btns mx-md-4 ">
                                        <input type="radio" class="form-check-input" id="solder" name="solderORcivil"
                                            value="military" style="height:20px; width:20px;" checked>
                                        <label class="form-check-label mx-2" for="solder">عسكرى</label>
                                    </div>
                                    <div class="radio-btns mx-md-4 ">
                                        <input type="radio" class="form-check-input" id="civil" name="solderORcivil"
                                            value="civil" style="height:20px; width:20px;">
                                        <label class="form-check-label mx-2" for="civil">مدنى</label>
                                    </div>
                                @endif
                                <label for="input44"> التصنيف</label>
                            </div>
                        </div>

                        <br>
                        <div class="form-row mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input1"><i class="fa-solid fa-asterisk" style="color:red; font-size:10px;"></i>  الاسم</label>
                                <input type="text" id="input1" name="name" class="form-control"
                                    placeholder="الاسم" value="{{ $user->name }}" dir="rtl">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input2"> <i class="fa-solid fa-asterisk" style="color:red; font-size:10px;"></i>   البريد الالكتروني</label>
                                <input type="text" id="input2" name="email" class="form-control"
                                    placeholder=" البريد الالكترونى" value="{{ $user->email }}" dir="rtl">
                            </div>
                        </div>

                        <div class="form-row mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input4"><i class="fa-solid fa-asterisk" style="color:red; font-size:10px;"></i>   رقم المحمول</label>
                                <input type="text" id="input4" name="phone" class="form-control"
                                    placeholder=" رقم المحمول" value="{{ $user->phone }}" dir="rtl">
                            </div>

                            <div class="form-group col-md-5 mx-2">
                                <label for="input6"> <i class="fa-solid fa-asterisk" style="color:red; font-size:10px;"></i>  رقم العسكرى</label>
                                <input type="text" id="input6" name="military_number" class="form-control"
                                    placeholder="رقم العسكرى" value="{{ $user->military_number }}" dir="rtl">
                            </div>
                        </div>

                        <div class="form-row mx-2 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-10 mx-2">
                                <label for="input8">الوظيفة</label>
                                <select id="input8" name="job" class="form-control select2" placeholder="المهام">
                                    <option  disabled>اختار من القائمة</option>
                                    @foreach ($job as $item)
                                    <option value="{{ $item->id }}" {{ $user->job_id == $item->id ? 'selected' : ''}}>
                                        {{ $item->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row  mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input9"> المسمي الوظيفي</label>
                                <input type="text" id="input9" name="job_title" class="form-control"
                                    placeholder="المسمي الوظيفي" value="{{ $user->job_title }}" dir="rtl">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input10">الجنسية</label>
                                <input type="text" id="input10" name="nationality" class="form-control"
                                    placeholder="الجنسية" value="{{ $user->nationality }}" dir="rtl">
                            </div>
                        </div>

                        <div class="form-row  mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input11"> <i class="fa-solid fa-asterisk" style="color:red; font-size:10px;"></i>  رقم المدنى</label>
                                <input type="text" id="input11" name="Civil_number" class="form-control"
                                    placeholder="رقم المدنى" value="{{ $user->Civil_number }}" dir="rtl">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input12"> <i class="fa-solid fa-asterisk" style="color:red; font-size:10px;"></i> رقم الملف</label>
                                <input type="text" id="input12" name="file_number" class="form-control"
                                    placeholder="رقم الملف" value="{{ $user->file_number }}" dir="rtl">
                            </div>
                        </div>

                        @if ($user->flag == "user")
                        <div class="form-row  mx-3 d-flex justify-content-center flex-row-reverse">

                            <div class="form-group col-md-5 mx-2">
                                <label for="input3">الباسورد</label>
                                <div class="password-container">
                                    <input type="password" id="input3" name="password" class="form-control" placeholder="الباسورد" style="position: absolute"  dir="rtl">
                                    <label class="toggle-password" onclick="togglePasswordVisibility()">
                                        <i id="toggleIcon" class="fa fa-eye eye-icon"></i>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-5 mx-2">
                                <label for="input7"> المهام</label>
                                <select id="input7" name="rule_id" class="form-control select2" placeholder="المهام">
                                    <option  disabled>اختار من القائمة</option>

                                    @foreach ($rule as $item)
                                    if($item->name != "localworkadmin")
                                    {
                                        <option value="{{ $item->id }}" {{ $user->rule_id == $item->id  ? 'selected' : ''}}>
                                            {{ $item->name }}</option>
                                    }
                                    
                                    @endforeach


                                </select>
                            </div>
                        </div>
                            {{-- <div class="form-row mx-2  d-flex justify-content-center flex-row-reverse">
                                <div class="form-group col-md-10 mx-2">
                                    <label for="input25"> القسم</label>
                                    <select id="input25" name="department_id" class="form-control select2"
                                        placeholder="القسم">
                                        <option  disabled>اختار من القائمة</option>

                                        @foreach ($department as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $user->department_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div> --}}
                        @endif

                        <div class="form-row mx-2 mx-2 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-10">
                                <label for="input13">هل يمكن لهذا لموظف ان يكون مستخدم ؟ </label>
                                {{-- <span>نعم : اختار مستخدم</span>
                                    <span>/</span>
                                    <span>لا : اختار موظف</span> --}}

                                <select id="input13" name="flag" class="form-control select2">
                                    @if ($user->flag == 'user')
                                        <option value="user" selected>مستخدم</option>
                                        {{-- <option value="employee">موظف</option> --}}
                                    @else
                                        {{-- <option value="user">مستخدم</option> --}}
                                        <option value="employee" selected>موظف</option>
                                    @endif
                                </select>
                            </div>
                        </div>



                        <div class="form-row mx-md-2  d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-md-2">
                                <label for="input44">العنوان 1</label>
                                <input type="text" id="input44" name="address_1" class="form-control"
                                    placeholder="  العنوان" value="{{ $user->address1 }}">
                            </div>
                            <div class="form-group col-md-5 mx-md-2">
                                <label for="input44">العنوان 2</label>
                                <input type="text" id="input44" name="address_2" class="form-control"
                                    placeholder="  العنوان" value="{{ $user->address2 }}">
                            </div>
                        </div>
                        <div class="form-row mx-md-2  d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="sector">قطاع </label>
                                {{-- <input type="text" id="input66" name="sector" class="form-control"
                                    placeholder="قطاع " value="{{ old('sector') }}" > --}}
                                    <select id="sector" name="sector" class="form-control select2" placeholder="المنطقة">
                                        <option selected value="null">اختار من القائمة</option>
                                        @foreach ($sector as $item)
                                            <option value="{{ $item->id }}" {{ $user->sector == $item->id ? 'selected' : '' }}> {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group col-md-5 mx-md-2">
                                <label for="Provinces"> المحافظة</label>
                                {{-- <input type="text" id="input44" name="Provinces" class="form-control"
                                    placeholder="  المحافظة" value="{{ $user->Provinces }}"> --}}

                                    <select id="Provinces" name="Provinces" class="form-control select2" placeholder="المحافظة">
                                        <option  disabled>اختار من القائمة</option>
                                        @foreach ($govermnent as $item)
                                            <option value="{{ $item->id }}" {{ $user->Provinces == $item->id ? 'selected' : '' }}> {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group col-md-5 mx-md-2">
                                <label for="region"> المنطقة</label>
                                {{-- <input type="text" id="region" name="region" class="form-control"
                                    placeholder="  المنطقة" value="{{ $user->region }}"> --}}
                                    <select id="region" name="region" class="form-control select2" placeholder="المنطقة">
                                        <option disabled >اختار من القائمة</option>
                                        @foreach ($area as $item)
                                            <option value="{{ $item->id }}" {{ $user->region == $item->id ? 'selected' : '' }}> {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                            </div>

                            <div class="form-group col-md-5 mx-2">
                                <label for="input22">مدة الخدمة</label>
                                <input type="date" id="input22" name="end_of_service" class="form-control"
                                    placeholder="مدة الخدمة " value="{{ $user->length_of_service }}">
                            </div>
                        </div>

                        <div class="form-row  mx-md-2 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-md-2">
                                <label for="input14">الاقدامية</label>
                                <input type="text" id="input14" name="seniority" class="form-control"
                                    placeholder="الاقدامية" value="{{ $user->seniority }}">
                            </div>

                            {{-- {{dd($department)}} --}}
                            <div class="form-group col-md-5 mx-2">
                                <label for="input15"> <i class="fa-solid fa-asterisk" style="color:red; font-size:10px;"></i>  الادارة </label>
                                <select id="input15" name="public_administration" class="form-control select2"

                                        placeholder="الادارة ">
                                    @if ($user->department_id == null)
                                    <option selected  disabled>اختار من القائمة</option>
                                    @endif


                                    @foreach ($department as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $user->department_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        <div class="form-row mx-2 mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input16">موقع العمل</label>
                                <input type="text" id="input16" name="work_location" class="form-control"
                                    placeholder="موقع العمل" value="{{ $user->work_location }}">
                            </div>

                                                {{-- <div class="form-group col-md-5 mx-2">
                                                        <label for="input17">المنصب</label>
                                                        <input type="text" id="input17" name="position" class="form-control"
                                                            placeholder="المنصب" value="{{ $user->position  }}">
                                    </div> --}}
                            <div class="form-group col-md-5 mx-2">
                                <label for="input18">المؤهل</label>
                                {{-- <input type="text" id="input18" name="qualification" class="form-control"
                                    placeholder="المؤهل" value="{{ $user->qualification }}"> --}}

                                    <select id="qualification" name="qualification" class="form-control" placeholder="المجموعة">
                                        <option selected disabled>اختار من القائمة</option>
                                        @foreach ($qualifications as $item)
                                            <option value="{{ $item->id }}"{{ $user->qualification == $item->id ? 'selected' : ''}}
                                               > {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>

                        <div class="form-row mx-2 mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input19">تاريخ الميلاد</label>
                                <input type="date" id="input19" name="date_of_birth" class="form-control"
                                    placeholder="تاريخ الميلاد" value="{{ $user->date_of_birth }}">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input20">تاريخ الالتحاق</label>
                                <input type="date" id="input20" name="joining_date" class="form-control"
                                    placeholder="تاريخ الالتحاق" value="{{ $user->joining_date }}">
                            </div>
                        </div>
                        {{-- <div class="form-group col-md-5 mx-2">
                                    <label for="input21">العمر</label>
                                    <input type="text" id="input21" name="age" class="form-control" placeholder="العمر"
                                        value="{{ $user->age  }}">
    </div> --}}

                        <div class="form-row mx-2 mx-2 d-flex justify-content-center flex-row-reverse">
                            

                            <div class="form-group col-md-10 mx-2">
                                <label for="input24"> الرتبة</label>
                                <select id="input24" name="grade_id" class="form-control select2" placeholder="الرتبة">
                                    @if ($user->grade_id == null)
                                    <option selected  disabled>اختار من القائمة</option>
                                    @endif
                                    @foreach ($grade as $item)
                                    <option value="{{ $item->id }}" {{ $user->grade_id == $item->id ? 'selected' : ''}}>
                                        {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row mx-2 mx-2 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-10">
                                <label for="input5"> الوصف</label>
                                <textarea type="text" id="input5" name="description" class="form-control" placeholder="الوصف"
                                    rows="3">{{ $user->description }}</textarea>
                            </div>
                        </div>
                        <div class="form-row mx-2 mx-2 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-10">
                                <label for="input23">الصورة</label>
                                <input type="file" class="form-control" name="image" id="input23"
                                    placeholder="الصورة" value="{{ $user->image }}">
                            </div>
                        </div>

                </div>

                <!-- Save button -->
                <div class="container col-10 mt-5 mb-5 ">
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
        </div>

    </section>
    <script>
    //     $(document).ready(function() {
    //     $('#sector').on('change', function() {
    //         var sector_id = $(this).val();
           
    
    //         if (sector_id) {
    //             $.ajax({
    //                 url: '/getGoverment/' + sector_id,
    //                 type: 'GET',
    //                 dataType: 'json',
    //                 success: function(data) {
    //                     $('#Provinces').empty();
    //                     $('#Provinces').append('<option selected> اختار من القائمة </option>');
    //                     $.each(data, function(key, employee) {               
    //                         console.log(employee);   
    //                         $('#Provinces').append('<option value="' + employee.id + '">' + employee.name + '</option>');
    //                     });                 
    //                 },
    //                 error: function(xhr, status, error) {
    //                     console.log('Error:', error);
    //                     console.log('XHR:', xhr.responseText);
    //                 }
    //             });
    //         } else {
    //             $('#Provinces').empty();
    //         }
    //     });
    // });

    $(document).ready(function() {
    function loadRegions() {
        var sector_id = $('#sector').val();
        // 
        var selectedProvincesId = '{{ $user->Provinces }}';
        if (sector_id) {
            $.ajax({
                url: '/getGoverment/' + sector_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#Provinces').empty();
                    $('#region').empty();
                    $('#Provinces').append('<option selected value="null"> اختار من القائمة </option>');
                    $.each(data, function(key, employee) {               
                        console.log(employee);   
                        var selected = (employee.id == selectedProvincesId) ? 'selected' : '';
                        $('#Provinces').append('<option value="' + employee.id + '" ' + selected + '>' + employee.name + '</option>');
                    });                 
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr.responseText);
                }
            });
        } else {
            $('#Provinces').empty();
            $('#region').empty();
        }
    }

    // Trigger loadRegions when Provinces changes
    $('#sector').on('change', loadRegions);

    // Trigger loadRegions when the page loads
    loadRegions();
});

$(document).ready(function() {
    function loadRegions() {
        var Provinces_id = $('#Provinces').val();
        var selectedregionId = '{{ $user->region }}';
        if (Provinces_id) {
            $.ajax({
                url: '/getRegion/' + Provinces_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#region').empty();
                    $('#region').append('<option selected value="null"> اختار من القائمة </option>');
                    $.each(data, function(key, employee) {               
                        console.log(employee);   
                        var selected = (employee.id == selectedregionId) ? 'selected' : '';
                        $('#region').append('<option value="' + employee.id + '"  ' + selected + '>' + employee.name + '</option>');
                    });                 
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr.responseText);
                }
            });
        } else {
            $('#region').empty();
        }
    }

    // Trigger loadRegions when Provinces changes
    $('#Provinces').on('change', loadRegions);

    // Trigger loadRegions when the page loads
    loadRegions();
});

    </script>
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById('input3');
            var toggleIcon = document.getElementById('toggleIcon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        </script>
        <script>
   // $(document).ready(function() {
    // $('.select2').select2({  dir: "rtl"});
//});
    </script>
      <script>
        $(document).ready(function() {
            $('.image-popup').click(function(event) {
                event.preventDefault();
                var imageUrl = $(this).data('image');
                var imageTitle = $(this).data('title');
    
                // Set modal image and title
                $('#modalImage').attr('src', imageUrl);
                $('#imageModalLabel').text(imageTitle);
    
                // Show the modal
                $('#imageModal').modal('show');
            });
        });
    </script>

@endsection
