
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>
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
    <link src="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    </link>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    @stack('style')
    <link rel="stylesheet" href="{{ asset('frontend/styles/index.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/styles/responsive.css') }}">
</head>

    <div class="all-nav">
        <div class="upper-navbar d-flex">
            <div class="second-section d-flex mx-1 col-md-9 col-sm-6">
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
                        <option value="{{ route('user.index' ,0) }}"> المستخدميين </option>
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
                    <div id="dropdownMenu3" class="dropdown-menu3">
                        <a href="#">الاجازات</a> <hr>
                        <a href="#">الشيفتات</a> 
                    </div>
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
                    <li class="nav-item">
                    <a href="{{ route('sub_departments.index') }}">
                        <img src="{{ asset('frontend/images/managements.svg') }}" alt="logo">
                        <h6>الاقسام</h6>
                        </a>
                    </li>
                 

                </ul>
            </div>
        </div>
    </div>
   
  

