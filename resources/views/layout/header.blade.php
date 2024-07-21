<!DOCTYPE html>
<html>

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
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/datatable/css/dataTables.dataTables.min.css') }}">

    </link>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    @stack('style')
    <link rel="stylesheet" href="{{ asset('frontend/styles/index.css') }}">
</head>

<body dir="rtl">
    <div class="all-nav">
        <div class="upper-navbar d-flex">
            <div class="first-section d-flex mt-1 ">
                <h2> الرقابة والتفتيش</h2>
                <img class="mt-2" src="{{ asset('frontend/images/logo.svg') }}" alt="">
            </div>
            <div class="second-section d-flex mx-4 col-md-9 col-sm-6">
               
                <div class="input-group">
                    <select name="#" id="#" class=" mt-4">
                        <option value="#"> المستخدميين </option>
                        <option value="#"> الادارات </option>
                        <option value="#"> الموظفين </option>
                        <option value="{{ route('Export.index') }}"> الصادر </option>
                        <option value="#"> الوارد </option>
                        <option value="#"> الاعدادات </option>

                    </select>
                    <div class="form-outline  mt-4">
                        <input type="search" id="" class="form-control" placeholder="بحث" />
                    </div>
                    <button type="button" class="btn  mt-4" data-mdb-ripple-init>
                        <i class="fas fa-search"></i>
                    </button>
                   
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
                <div class="dropdown">
                    <button class="btn btn-2  mt-3" onclick="toggleDropdown()">
                        <i class="fa-solid fa-angle-down mx-2"></i>
                        اسم المستخدم
                        <i class="fa-solid fa-user mx-2"></i>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu">
                        <a href="{{ route('logout') }}">تسجيل خروج <i class="fa-solid fa-right-from-bracket"></i></a>
                    </div>
                    <select name="#" id="#" class=" mt-4">
                        <option value="#"> المستخدميين </option>
                        <option value="{{ route('departments.index') }}"> الادارات </option>
                        <option value="#"> التعيينات </option>
                        <option value="#"> الموظفين </option>
                        <option value="{{ route('Export.index') }}"> الصادر </option>
                        <option value="#"> الوارد </option>
                    </select>

                </div>
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
                        <a href="{{route('home')}}">
                        <img src="{{ asset('frontend/images/home.svg') }}" alt="logo">
                        <h6>الرئيسية</h6>
                        </a>
                    </li>
                    <li class="nav-item">
                        <img src="{{ asset('frontend/images/users.svg') }}" alt="logo">
                        <h6>المستخدمين</h6>
                    </li>
                   
                    <li class="nav-item">
                        <img src="{{ asset('frontend/images/employees.svg') }}" alt="logo">
                        <h6>الموظفين</h6>
                    </li>
                 
                    <li class="nav-item">
                        <a href="{{route('departments.index')}}">
                        <img src="{{ asset('frontend/images/managements.svg') }}" alt="logo">
                        <h6>الادارات</h6>
                        </a>
                    </li>
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
                        <a href="{{route('settings.index')}}">
                        <img src="{{ asset('frontend/images/managements.svg') }}" alt="logo">
                        <h6>الاعدادات</h6>
                        </a>
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
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {

            $("#saveExternalDepartment").on("submit", function(e) {

                e.preventDefault();

                // Serialize the form data
                var formData = $(this).serialize(); // Changed to $(this)

                // Submit AJAX request
                $.ajax({
                    url: $(this).attr('action'), // Changed to $(this)
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Handle success response
                        console.log(response);
                        $('#from_departement').empty();
                        $.ajax({

                            url: "{{ route('external.departments') }}",
                            type: 'get',
                            success: function(response) {
                                // Handle success response
                                var selectOptions =
                                    '<option value="">اختر الادارة</option>';
                                response.forEach(function(department) {
                                    selectOptions += '<option value="' +
                                        department.id +
                                        '">' + department.name +
                                        '</option>';
                                });
                                $('#from_departement').html(
                                    selectOptions
                                ); // Assuming you have a select element with id 'from_departement'

                            },
                            error: function(xhr, status, error) {
                                // Handle error response
                                console.error(xhr.responseText);
                            }
                        });
                        // Optionally, you can close the modal after successful save
                        $('#extern-department').modal('hide'); // Changed modal ID
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
                    }
                });
            });
        });

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
    </script>
</body>

</html>
