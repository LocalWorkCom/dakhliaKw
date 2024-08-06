<div class="all-nav">
    <div class="upper-navbar d-flex">
        <div class="second-section d-flex  col-md-10 col-sm-6">
            <div class="dropdown">
                {{-- @if ($user->login) Test --}}
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
                        <a href="{{ route('logout') }}">تسجيل خروج <i class="fa-solid fa-right-from-bracket"></i></a>
                    </div>
                @else
                    <button class="btn btn-2 mt-3">
                        <a href="{{ route('login') }}" style="color: #ffffff; text-decoration:none;">سجل الدخول <i
                                class="fa-solid fa-user mx-2"></i></a>
                    </button>
                @endif



            </div>
            <button class="btn2 btn-2 mx-5" style="    border-inline: 1px solid rgb(41, 41, 41); height: 100%;"
                onclick="toggleDropdown2()">
                <a class="bell mx-md-5">
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
            <div class="input-group mx-2">
                <button type="button" class="btn  mt-4" data-mdb-ripple-init>
                    <i class="fas fa-search"></i>
                </button>
                <div class="form-outline  mt-4">
                    <input type="search" id="" class="form-control" placeholder="بحث" />
                </div>
                <select name="#" id="#" class=" mt-4" style="direction:rtl;">
                    <option value="#"> المستخدميين </option>
                    <option value="{{ route('departments.index') }}"> الادارات </option>

                    <option value="#"> الموظفين </option>
                    <option value="{{ route('Export.index') }}"> الصادر </option>
                    <option value="#"> الوارد </option>
                </select>
            </div>
        </div>
        <div class="first-section d-flex justify-content-between mt-1 ">
            
            <h2 style="color: #ffffff">{{ showUserDepartment() }}  -</h2>
     
            <h2> الرقابة والتفتيش</h2>
            <img class="mt-2" src="{{ asset('frontend/images/logo.svg') }}" alt="">
            
        </div>
    </div>
    <div class="navbar navbar-expand-md mb-4" role="navigation" dir="rtl">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars side-nav"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav  d-flex justify-content-between w-100">
                <li class="nav-item">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('frontend/images/home.svg') }}" alt="logo">
                        <h6>الرئيسية</h6>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('user.index', 0) }}">
                        <img src="{{ asset('frontend/images/users.svg') }}" alt="logo">
                        <h6>المستخدمين</h6>
                    </a>
                </li>

                <div>
                    <li class="nav-item btn3" onclick="toggleDropdown3(event)">
                        <a href="{{ route('user.employees', 1) }}">
                            <img src="{{ asset('frontend/images/employees.svg') }}" alt="logo">
                            <h6 class="btn3">الموظفين </h6>
                        </a>
                        <!-- قائمة منسدلة -->
                        {{-- <div id="dropdownMenu3" class="dropdown-menu3">
                            <ul> --}}
                        {{-- <li>
                                    <img src="{{ asset('frontend/images/employee.svg') }}" alt="logo"
                                        style="margin-left: 7px;">
                                    <a href="{{ route('user.employees', 1) }}">الموظفين</a>
                                </li>
                                <li> --}}
                        {{-- <img src="{{ asset('frontend/images/weekend.png') }}" alt="logo"
                                    style="margin-left: 7px;">
                                    <a href="{{ route('vacations.list') }}">الاجازات</a>
                                    --}}
                        {{-- </li>
                            </ul>
                        </div>
                    </li> --}}
                </div>

                <div>
                    <li class="nav-item btn5" onclick="toggleDropdown5(event)">
                        <a href="#">
                            <img src="{{ asset('frontend/images/moftsheen.svg') }}" alt="logo">
                            <h6 class="btn5"> التفتيش<i class="fa-solid fa-angle-down"></i></h6>
                        </a>

                        <div id="dropdownMenu5" class="dropdown-menu5">
                            <ul>
                                <li>
                                    <img src="{{ asset('frontend/images/inspectors.svg') }}" alt="logo"
                                        style="margin-left: 7px;">
                                    <a href="{{ route('inspectors.index') }}">المفتشون</a>
                                </li>
                                <li>
                                    <img src="{{ asset('frontend/images/groups.svg') }}" alt="logo"
                                        style="margin-left: 7px;">
                                    <a href="{{ route('group.view') }}"> المجموعات</a>
                                </li>




                            </ul>
                        </div>
                    </li>
                </div>


                @php
                    use App\Models\departements;
                    $checksubDepartment = departements::find(Auth::user()->department_id);
                @endphp

                @if (Auth::user()->hasPermission('view departements'))
                    <!--   <li class="nav-item">
                    <a href="{{ route('sub_departments.index') }}">
                        <img src="{{ asset('frontend/images/departments.svg') }}" alt="logo">
                        <h6>الاقسام</h6>
                    </a>
                </li> -->
                @endif

                @if (Auth::user()->hasPermission('view departements') && Auth::user()->rule_id == 2)
                    <li class="nav-item">
                        <a href="{{ route('departments.index') }}">
                            <img src="{{ asset('frontend/images/managements.svg') }}" alt="logo">
                            <h6>الادارات</h6>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->hasPermission('view job') ||
                        Auth::user()->hasPermission('view VacationType') ||
                        Auth::user()->hasPermission('view grade') ||
                        Auth::user()->hasPermission('view Government') ||
                        Auth::user()->hasPermission('view Rule') ||
                        Auth::user()->hasPermission('view Permission'))
                    <div>



                        <li class="nav-item" onclick="toggleDropdown4(event)">


                            <a href="#">
                                <img src="{{ asset('frontend/images/settings.svg') }}" alt="logo">
                                <h6 class="btn4">الإعدادات <i class="fa-solid fa-angle-down"></i></h6>
                            </a>

                            <!-- قائمة منسدلة -->
                            <div id="dropdownMenu4" class="dropdown-menu4">
                                <ul>
                                    <div class="row col-12 d-flex justify-content-around">
                                        <div class="col-6">
                                            @if (Auth::user()->hasPermission('view grade'))
                                                <li>
                                                    <img src="{{ asset('frontend/images/police.svg') }}"
                                                        alt="logo" style="margin-left: 7px;">
                                                    <a href="{{ route('grads.index') }}">الرتب العسكرية</a>
                                                </li>
                                            @endif

                                            @if (Auth::user()->hasPermission('view job'))
                                                <li>
                                                    <img src="{{ asset('frontend/images/jobs.svg') }}" alt="logo"
                                                        style="margin-left: 7px;">
                                                    <a href="{{ route('job.index') }}">الوظائف</a>
                                                </li>
                                            @endif
                                            @if (Auth::user()->hasPermission('view Qualification'))
                                                <li>
                                                    <img src="{{ asset('frontend/images/governorates.svg') }}"
                                                        alt="logo" style="margin-left: 7px;">
                                                    <a href="{{ route('qualifications.index') }}">المؤهلات</a>
                                                </li>
                                            @endif
                                            @if (Auth::user()->hasPermission('view Government'))
                                                <li>
                                                    <img src="{{ asset('frontend/images/governorates.svg') }}"
                                                        alt="logo" style="margin-left: 7px;">
                                                    <a href="{{ route('government.all') }}">المحافظات</a>
                                                </li>
                                            @endif
                                            <li>
                                                <img src="{{ asset('frontend/images/holidays.svg') }}" alt="logo"
                                                    style="margin-left: 7px;">
                                                <a href="{{ route('violations.index') }}">أنواع المخالفات</a>
                                            </li>

                                            {{-- @if (Auth::user()->hasPermission('view Point')) --}}
                                            <li>
                                                <img src="{{ asset('frontend/images/governorates.svg') }}"
                                                    alt="logo" style="margin-left: 7px;">
                                                <a href="{{ route('points.index') }}">النقاط</a>
                                            </li>
                                            {{-- @endif --}}
                                        </div>
                                        <div class="col-6">


                                            @if (Auth::user()->hasPermission('view VacationType'))
                                                <li>
                                                    <img src="{{ asset('frontend/images/holidays.svg') }}"
                                                        alt="logo" style="margin-left: 7px;">
                                                    <a href="{{ route('vacationType.index') }}">أنواع الأجازات</a>
                                                </li>
                                            @endif
                                            <!-- @if (Auth::user()->hasPermission('view VacationType'))
-->

                                            <!--
@endif -->
                                            @if (Auth::user()->hasPermission('view Rule'))
                                                <li>
                                                    <img src="{{ asset('frontend/images/task.svg') }}" alt="logo"
                                                        style="margin-left: 7px;">
                                                    <a href="{{ route('rule.index') }}">المهام</a>
                                                </li>
                                            @endif
                                            @if (Auth::user()->hasPermission('view Permission'))
                                                <li>
                                                    <img src="{{ asset('frontend/images/permission.svg') }}"
                                                        alt="logo" style="margin-left: 7px;">
                                                    <a href="{{ route('permission.index') }}">الصلاحيات</a>
                                                </li>
                                            @endif
                                            <li>
                                                <img src="{{ asset('frontend/images/governorates.svg') }}"
                                                    alt="logo" style="margin-left: 7px;">
                                                <a href="{{ route('working_time.index') }}">فترات العمل </a>
                                            </li>
                                            <li>
                                                <img src="{{ asset('frontend/images/permission.svg') }}"
                                                    alt="logo" style="margin-left: 7px;">
                                                <a href="{{ route('working_trees.list') }}">نظام العمل</a>
                                            </li>

                                            {{-- <li>
                                        <img src="{{ asset('frontend/images/police.svg') }}" alt="logo"
                                        style="margin-left: 7px;">
                                        <a href="{{ route('inspectors.index') }}">المفتشون</a>
                    </li> --}}
                                            {{-- @if (Auth::user()->hasPermission('view Region')) --}}
                                            <li>
                                                <img src="{{ asset('frontend/images/governorates.svg') }}"
                                                    alt="logo" style="margin-left: 7px;">
                                                <a href="{{ route('regions.index', ['id' => 0]) }}">المناطق</a>
                                            </li>
                                            {{-- @endif --}}
                                            {{-- @if (Auth::user()->hasPermission('view Sector')) --}}
                                            <li>
                                                <img src="{{ asset('frontend/images/governorates.svg') }}"
                                                    alt="logo" style="margin-left: 7px;">
                                                <a href="{{ route('sectors.index') }}">القطاعات</a>
                                            </li>
                                            {{-- @endif --}}
                                        </div>
                                    </div>

                                </ul>
                            </div>
                            {{-- @endif --}}
                        </li>

                        {{-- --}}

                    </div>
                @endif

                @if (Auth::user()->hasPermission('view Iotelegram'))
                    <li class="nav-item">

                        <a href="{{ route('iotelegrams.list') }}">
                            <img src="{{ asset('frontend/images/imports.svg') }}" alt="logo">
                            <h6>الوارد</h6>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->hasPermission('view outgoings'))
                    <li class="nav-item">
                        <a href="{{ route('Export.index') }}">
                            <img src="{{ asset('frontend/images/exports.svg') }}" alt="logo">
                            <h6>الصادر</h6>

                        </a>
                    </li>
                @endif



            </ul>
        </div>
    </div>
</div>
