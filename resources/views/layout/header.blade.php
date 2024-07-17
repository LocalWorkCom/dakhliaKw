<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    @stack('style')
    <link rel="stylesheet" href="{{ asset('frontend/styles/index.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/styles/responsive.css') }}">
</head>

<body>
    <div class="all-nav">
    <div class="upper-navbar d-flex">
    <div class="second-section d-flex mx-md-4 mx-sm-0 col-md-9 col-sm-6">

                <div class="dropdown">

                <button class="btn btn-2  mt-3" onclick="toggleDropdown()"> 
                <i class="fa-solid fa-angle-down md-mx-2"></i>
                        اسم المستخدم
                        <i class="fa-solid fa-user md-mx-2 "></i>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu">
                        <a href="{{ route('logout') }}">تسجيل خروج <i class="fa-solid fa-right-from-bracket"></i></a>

                    </div>
                </div>
                <button class="btn2 btn-2 mx-5" style="    border-inline: 1px solid rgb(41, 41, 41); height: 100%;"
                    onclick="toggleDropdown2()">
                    <a class="bell mx-5">
                    <i class=" fa-regular fa-bell bell md-mx-5" ></i>
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

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars" ></i>
          </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ml-auto">

                    <li class="nav-item" onclick="makeActive(this)" >
                        <a href="{{ route('iotelegrams.list') }}">
                            <img src="{{ asset('frontend/images/exports.svg') }}" alt="logo">
                            <h6 class="nav-link">الوارد</h6>
                        </a>
                    </li>
                    <li class="nav-item" onclick="makeActive(this)">
                        <a href="{{ route('Export.index') }}">
                        <img src="{{ asset('frontend/images/imports.svg') }}" alt="logo">
                        <h6 class="nav-link">الصادر</h6>
                        </a>
                    </li>
                    <li class="nav-item" onclick="makeActive(this)">
                        <img src="{{ asset('frontend/images/employees.svg') }}" alt="logo">
                        <h6 class="nav-link">الموظفين</h6>
                    </li>
                    <!-- <li class="nav-item" onclick="makeActive(this)">
                        <img src="{{ asset('frontend/images/managements.svg') }}" alt="logo">
                        <h6>التعيينات</h6>
                    </li> -->
                    <li class="nav-item" onclick="makeActive(this)">
                        <img src="{{ asset('frontend/images/managements.svg') }}" alt="logo">
                        <h6 class="nav-link">الادارات</h6>
                    </li>
                    <li class="nav-item" onclick="makeActive(this)">
                        <img src="{{ asset('frontend/images/users.svg') }}" alt="logo">
                        <h6 class="nav-link">المستخدمين</h6>
                    </li>
                    <li class="nav-item" onclick="makeActive(this)">
                        <img src="{{ asset('frontend/images/home.svg') }}" alt="logo">
                        <h6 class="nav-link">الرئيسية</h6>
                    </li>

                </ul>

            </div>
        </div>


    </div>


    <main>
        @yield('content')
    </main>
    @stack('scripts')

    <br> <br> <br> <br>
    <footer class="my-2">
        <div class="footer ">
            <p>جميع الحقوق محفوظه </p>
        </div>
    </footer>
    
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        function toggleDropdown() {
            var dropdownMenu = document.getElementById("dropdownMenu");
            if (dropdownMenu.style.display === "block") {
                dropdownMenu.style.display = "none";
            } else {
                dropdownMenu.style.display = "block";
            }
        }

        window.onclick = function(event) {
            if (!event.target.matches('.btn')) {
                var dropdowns = document.getElementsByClassName("dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === "block") {
                        openDropdown.style.display = "none";
                    }
                }
            }
        }

        function toggleDropdown2() {
            var dropdownMenu = document.getElementById("dropdownMenu2");
            if (dropdownMenu.style.display === "block") {
                dropdownMenu.style.display = "none";
            } else {
                dropdownMenu.style.display = "block";
            }
        }

        window.onclick = function(event) {
            if (!event.target.matches('.btn2')) {
                var dropdowns = document.getElementsByClassName("dropdown-menu2");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === "block") {
                        openDropdown.style.display = "none";
                    }
                }
            }
        }
        function makeActive(element) {
    var navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(link) {
        link.classList.remove('active-page');
    });
    var navLink = element.querySelector('.nav-link');
    navLink.classList.add('active-page');
}
    </script>
</body>

</html>
