<!DOCTYPE html>
<html>
<head>
    <title>User DataTable</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>


    <script type="application/javascript" src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap-->
    <link href="{{ asset('frontend/styles/bootstrap.min.css') }}" rel="stylesheet" id="bootstrap-css">
    {{-- <link src="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    </link> --}}
    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
    @stack('style')
    <link rel="stylesheet" href="{{ asset('frontend/styles/index.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/styles/responsive.css') }}">


</head>
<body>

    <div class="all-nav">
        <div class="upper-navbar d-flex">
            <div class="second-section d-flex mx-4 col-md-9 col-sm-6">
                <div class="dropdown">
                    {{-- @if ($user->login) --}}
                    @php
                        $user = auth()->user();
                    @endphp

                    @if (!empty($user))
                        <button class="btn btn-2 mt-3" onclick="toggleDropdown()">
                            <i class="fa-solid fa-angle-down mx-2"></i>
                            {{ $user->name }}
                            <i class="fa-solid fa-user mx-2"></i>
                        </button>
                        <div id="dropdownMenu" class="dropdown-menu">
                            <a href="{{ route('logout') }}">تسجيل خروج <i
                                    class="fa-solid fa-right-from-bracket"></i></a>
                        </div>
                    @else
                    <button class="btn btn-2 mt-3" >
                        <a href="{{ route('login') }}" style="color: #ffffff; text-decoration:none;">سجل الدخول <i class="fa-solid fa-user mx-2"></i></a>
                    </button>

                    @endif



                </div>
                <button class="btn2 btn-2 mx-5" style="    border-inline: 1px solid rgb(41, 41, 41); height: 100%;"
                    onclick="toggleDropdown2()">
                    <a class="bell mx-5">
                        <i class=" fa-regular fa-bell"></i>
                    </a>
                </button>
                <div id="dropdownMenu2" class="dropdown-menu2">
                    <p>notification notification notification notification </p>
                    <hr>
                    <p>notification notification notification notification </p>
                    <hr>
                    <p>notification notification notification notification </p>
                    <hr>
                    <p>notification notification notification notification </p>
                    <hr>
                    <p>notification notification notification notification </p>
                    <hr>
                </div>
                <div class="input-group">
                    <button type="button" class="btn  mt-4" data-mdb-ripple-init>
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="form-outline  mt-4">
                        <input type="search" id="" class="form-control" placeholder="بحث" />
                    </div>
                    <select name="#" id="#" class=" mt-4">
                        <option value="#"> المستخدميين </option>
                        <option value="#"> الادارات </option>
                        <option value="#"> التعيينات </option>
                        <option value="#"> الموظفين </option>
                        <option value="{{ route('Export.index') }}"> الصادر </option>
                        <option value="#"> الوارد </option>
                    </select>
                </div>
            </div>
            <div class="first-section d-flex mt-1 ">
                <h2> الرقابة والتفتيش</h2>
                <img class="mt-2" src="{{ asset('frontend/images/logo.svg') }}" alt="">
            </div>
        </div>
        <div class="navbar navbar-expand-md mb-4 w-100" role="navigation">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="{{ route('iotelegrams.list') }}">
                            <img src="{{ asset('frontend/images/exports.svg') }}" alt="logo">
                            <h6>الوارد</h6>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('Export.index') }}">
                            <img src="{{ asset('frontend/images/imports.svg') }}" alt="logo">
                            <h6>الصادر</h6>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.employees',1) }}">
                            <img src="{{ asset('frontend/images/employees.svg') }}" alt="logo">
                            <h6>الموظفين</h6>
                        </a>
                        
                    </li>
                    <li class="nav-item">
                        <img src="{{ asset('frontend/images/managements.svg') }}" alt="logo">
                        <h6>التعيينات</h6>
                    </li>
                    <li class="nav-item">
                        <img src="{{ asset('frontend/images/managements.svg') }}" alt="logo">
                        <h6>الادارات</h6>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.index',0) }}">
                            <img src="{{ asset('frontend/images/users.svg') }}" alt="logo">
                            <h6>المستخدمين</h6>
                        </a>
                    </li>
                    <li class="nav-item">
                        <img src="{{ asset('frontend/images/home.svg') }}" alt="logo">
                        <h6>الرئيسية</h6>
                    </li>
                </ul>
            </div>
        </div>
    </div>
     
    <div class="container-fluid p-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="bg-white p-5 text-right">
                    <form>
                        <div class="row m-5 d-flex flex-row-reverse">
                          <div class="col">
                            <label for="input1"> الاسم </label>
                            <input type="text" class="form-control" id="input1" name="name" placeholder="الاسم" value="{{ $user->name  }}">
                          </div>
                          <div class="col">
                            <label for="input2"> البريد الالكترونى</label>
                            <input type="text" class="form-control" id="input2" name="email" placeholder=" البريد الالكترونى" value="{{ $user->email  }}">
                          </div>
                        </div>
                        <br>
                        <div class="row m-5">
                            <div class="col">
                              <label for="input3"> الباسورد</label>
                              <input type="password" class="form-control" id="input3" name="password" placeholder="الباسورد" value="{{ $user->password  }}">
                            </div>
                            <div class="col">
                              <label for="input4"> رقم المحمول</label>
                              <input type="text" class="form-control" name="phone" id="input4" placeholder=" رقم المحمول" value="{{ $user->phone  }}">
                            </div>
                        </div>
                          <br>
                        <div class="row m-5">
                            <div class="col">
                              <div class="form-group">
                                <label for="input5"> الوصف</label>
                                <textarea class="form-control" id="input5" name="description" placeholder="الوصف" rows="3">{{ $user->description }}</textarea>
                              </div>
                            </div>
                            <div class="col">
                              <label for="input6">رقم العسكرى</label>
                              <input type="text" class="form-control" name="military_number" placeholder="رقم العسكرى" id="input6" value="{{ $user->military_number }}">
                            </div>
                        </div>
                          <br>
                          <div class="row m-5">
                            <div class="col">
                              <label for="input7">الادوار</label>
                              <select name="rule_id" id="input7">
                                <option value=""></option>
                               
                              </select>
                              
                            </div>
                            <div class="col">
                              <label for="input8">الوظيفة</label>
                              <input type="text" class="form-control" name="job" id="input8" placeholder="الوظيفة" value="{{ $user->job }}">
                            </div>
                          </div>
                          <br>
                          <div class="row m-5">
                            <div class="col">
                              <label for="input9">المسمى الوظيفى</label>
                              <input type="text" class="form-control" name="job_title" id="input9" placeholder="المسمى الوظيفى" value="{{ $user->job_title }}">
                            </div>
                            <div class="col">
                              <label for="input10">الجنسية</label>
                              <input type="text" class="form-control" name="nationality" id="input10" placeholder="الجنسية" value="{{ $user->nationality }}">
                            </div>
                          </div>
                          <br>
                          <div class="row m-5">
                            <div class="col">
                              <label for="input11"> رقم المدنى</label>
                              <input type="text" class="form-control" name="Civil_number" id="input11" placeholder="رقم المدنى" value="{{ $user->Civil_number }}">
                            </div>
                            <div class="col">
                              <label for="input12">رقم الملف</label>
                              <input type="text" class="form-control" name="file_number" id="input12" placeholder="رقم الملف" value="{{ $user->file_number }}">
                            </div>
                          </div>
                          <br>
                          <div class="row m-5">
                            <div class="col">
                                <div class="form-group">
                                    
                                    <span>نعم : اختار مستخدم</span>
                                    <span>/</span>
                                    <span>لا : اختار موظف</span>
                                    <label for="input13">هل يمكن لهذا لموظف ان يكون مستخدم ؟ </label>
                                    <select class="form-control" name="flag" id="input13">
                                        <option value="user">مستخدم</option>
                                        <option value="employee">موظف</option>
                                    </select>
                                  </div>
                            </div>
                            <div class="col">
                              <label for="input14">الاقدامية</label>
                              <input type="text" class="form-control" name="seniority" id="input14" placeholder="الاقدامية" value="{{ $user->seniority }}">
                            </div>
                          </div>
                          <br>
                          <div class="row m-5">
                            <div class="col">
                                <div class="form-group">
                                    
                        
                                    <label for="input15">الادارة العامة</label>
                                    <select class="form-control" name="public_administration" id="input15">
                                        <option value=""></option>
                                        <option value=""></option>
                                        <option value=""></option>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                              <label for="input16">موقع العمل</label>
                              <input type="text" class="form-control" name="work_location" id="input16" placeholder="موقع العمل" value="{{ $user->work_location	 }}">
                            </div>
                          </div>
                          <br>
                          <div class="row m-5">
                            <div class="col">
                              <label for="input17">المنصب</label>
                              <input type="text" class="form-control" name="position" id="input17" placeholder="المنصب" value="{{ $user->position }}">
                            </div>
                            <div class="col">
                              <label for="input18">المؤهل</label>
                              <input type="text" class="form-control" name="qualification" id="input18" placeholder="المؤهل" value="{{ $user->qualification }}">
                            </div>
                          </div>
                          <br>
                          <div class="row m-5">
                            <div class="col">
                              <label for="input19">تاريخ الميلاد</label>
                              <input type="date" class="form-control" name="date_of_birth" id="input19" placeholder="تاريخ الميلاد" value="{{ $user->date_of_birth }}">
                            </div>
                            <div class="col">
                              <label for="input20">تاريخ الالتحاق</label>
                              <input type="data" class="form-control" name="joining_date" id="input20" placeholder="تاريخ الالتحاق" value="{{ $user->joining_date }}">
                            </div>
                          </div>
                          <br>
                          <div class="row m-5">
                            <div class="col">
                              <label for="input21">العمر</label>
                              <input type="text" class="form-control" name="age" id="input21" placeholder="العمر" value="{{ $user->age }}">
                            </div>
                            <div class="col">
                              <label for="input22">مدة الخدمة </label>
                              <input type="date" class="form-control" name="end_of_service" id="input22" placeholder="مدة الخدمة " value="{{ $user->length_of_service }}">
                            </div>
                          </div>
                          <br>

                          <div class="row m-5">
                            <div class="col">
                              <label for="input23">الصورة</label>
                              <input type="file" class="form-control" name="image" id="input23" placeholder="الصورة">
                            </div>
                            <div class="col">
                              <label for="input24">الرتبة</label>
                              {{-- <input type="date" class="form-control" name="grade_id" id="input24" placeholder="مدة الخدمة " value="{{ $user->length_of_service }}"> --}}
                              <select class="form-control" name="grade_id" id="input24">
                                <option value=""></option>
                                <option value=""></option>
                                <option value=""></option>
                                <option value=""></option>
                            </select>
                            </div>
                          </div>
                          <br>

                          <div class="row m-5">
                            <div class="col">
                              <label for="input25">القسم</label>
                              {{-- <input type="date" class="form-control" name="grade_id" id="input24" placeholder="مدة الخدمة " value="{{ $user->length_of_service }}"> --}}
                              <select class="form-control" name="department_id" id="input25">
                                <option value=""></option>
                                <option value=""></option>
                                <option value=""></option>
                                <option value=""></option>
                            </select>
                            </div>
                          </div>
                          <br>
                        
                        
                      </form>
                </div>
            </div>
        </div>
        
    </div>

   
</body>
</html>
