<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap-->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('frontend/styles/main.css') }}">
</head>

<body>



    <!-- <nav class="navbar navbar-expand-lg" style="background-color:#FFFFFF;">

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav" style="display: flex; align-items: center;">
            <li style="margin-right: 10px;">
              <img src="{{ asset('frontend/images/home.jpg') }}" alt="logo" style="width: 24px; height: 24px;">
              <h6>الرئيسية</h6>
            </li>
            <li style="margin-right: 10px;">
              <img src="{{ asset('frontend/images/users.webp') }}" alt="logo" style="width: 24px; height: 24px;">
              <h6>المستخدمين</h6>
            </li>
            <li style="margin-right: 10px;">
              <img src="{{ asset('frontend/images/management.png') }}" alt="logo" style="width: 24px; height: 24px;">
              <h6>الادارات</h6>
            </li>
            <li style="margin-right: 10px;">
              <img src="{{ asset('frontend/images/cats.png') }}" alt="logo" style="width: 24px; height: 24px;">
              <h6>التعيينات</h6>
            </li>
            <li style="margin-right: 10px;">
              <img src="{{ asset('frontend/images/employees.svg') }}" alt="logo" style="width: 24px; height: 24px;">
              <h6>الموظفين</h6>
            </li>
            <li style="margin-right: 10px;">
              <img src="{{ asset('frontend/images/exports.svg') }}" alt="logo" style="width: 24px; height: 24px;">
              <h6>الصادر</h6>
            </li>
            <li style="margin-right: 10px;">
              <img src="{{ asset('frontend/images/import.svg') }}" alt="logo" style="width: 24px; height: 24px;">
              <h6>الوارد</h6>
            </li>
            <!--
            <li style="margin-right: 10px;">
              <img src="" alt="logo" style="width: 24px; height: 24px;">
              <h6>المزيد</h6>
            </li>
            -->
    <!-- </ul>
      
        </div>
      </nav> -->

    <div class="all-nav">
        <div class="upper-navbar">
            <div class="row ">

                <div class="second-section d-flex mx-3 mt-5 col-md-9 col-sm-6">
                    <button class="btn-1 mx-2">تسجيل خروج
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                    <button class="btn-2 mx-2">اسم المستخدم
                        <i class="fa-solid fa-user"></i>
                    </button>
                    <a class="bell mx-5">
                        <i class=" fa-regular fa-bell"></i>
                    </a>
                </div>
                <div class="first-section d-flex">
                    <h2>شئون القوة</h2>
                    <img src="{{ asset('frontend/images/logooo.png') }}" alt="">
                </div>
            </div>
        </div>



        <hr>
        <div class="navbar navbar-expand-md  mb-4" role="navigation">

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav">

                    <li style="margin-right: 10px;width: 260px;">
                        <img src="{{ asset('frontend/images/import.svg') }}" alt="logo" style="">
                        <h6>الوارد</h6>
                    </li>
                    <li style="margin-right: 10px;width: 260px;">
                        <img src="{{ asset('frontend/images/exports.svg') }}" alt="logo" style="">
                        <h6>الصادر</h6>
                    </li>
                    <li style="margin-right: 10px;width: 260px;">
                        <img src="{{ asset('frontend/images/employees.svg') }}" alt="logo" style="">
                        <h6>الموظفين</h6>
                    </li>
                    <li style="margin-right: 10px;width: 260px;">
                        <img src="{{ asset('frontend/images/cats.png') }}" alt="logo" style="">
                        <h6>التعيينات</h6>
                    </li>
                    <li style="margin-right: 10px; width: 260px;">
                        <img src="{{ asset('frontend/images/management.png') }}" alt="logo" style="">
                        <h6>الادارات</h6>
                    </li>
                    <li style="margin-right: 10px; width: 260px;">
                        <img src="{{ asset('frontend/images/users.webp') }}" alt="logo" style="">
                        <h6>المستخدمين</h6>
                    </li>
                    <li style="margin-right: 10px; width: 260px;">
                        <img src="{{ asset('frontend/images/home.jpg') }}" alt="logo" style="">
                        <h6>الرئيسية</h6>
                    </li>

                </ul>

            </div>
        </div>
        <hr>
    </div>
    <main>

    </main>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-zEYs/p5zCUo7LHibzS2KkETvP3L3PaZGvZLme7w+FVZ+Uk2x/E7l3niFf5XFk6ew" crossorigin="anonymous">
    </script>

</body>

</html>
