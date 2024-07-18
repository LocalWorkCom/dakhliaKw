<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>هيئة </title>
    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap-->
    <link rel="stylesheet" href="./assets/vendor/bootstrap/css/bootstrap.min.css.map" type="text/css" />
    <link rel="stylesheet" href="./assets/vendor/bootstrap/css/bootstrap.min.css" type="text/css" />

    <!-- CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('frontend/styles/login-styles.css') }}"> -->
    <!-- <link rel="stylesheet" href="{{ asset('frontend/styles/login-responsive.css') }}"> -->
</head>

<body>

    <section>
        <!-- <div class="container pt-4 col-10 " syle="background-color:transparent;        max-width: 1300px; "> -->
        <div class="row col-12">
            <div class=" col-md-4 col-sm-2">
                <img src="{{ asset('frontend/images/logo.svg') }}" alt="logo" class="logo">
            </div>
            <div class=" col-md-8 col-sm-10">
                <h5 class="login-h5">وزارة الداخلــــــــــــــــــية</h5>
                <p class="login-p">الادارة العامة لشئون قوة الشرطة</p>
                <h2 class="login-h2">المطــــور</h2>
            </div>
        </div>
        <div class="row col-12 d-flex justify-content-between">
            <div class="col-5 col-md-5">
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
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
                    <label for="username" class="login-label"> رقم العسكري</label> <br>
                    <input type="text" name="military_number" id="username" class="login-input"><br>
                    <label for="password" class="login-label">كلمة المرور</label><br>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="login-input">
                        <!-- <span class="eye-icon" id="togglePassword">
                        <i class="fa fa-eye"></i>
                    </span> -->
                    </div>
                    <br>
                    <div class="btns d-flex justify-content-between ">
                        <button class="btn1" type="submit">تسجيل دخول</button>
                        &nbsp; &nbsp; &nbsp;
                        {{-- <button class="btn2"><i class="fa-solid fa-right-from-bracket"></i>  &nbsp; تسجيل خروج</button> --}}

                        <button class="btn2" type="button" onclick="location.href='{{ route('forget_password') }}'">
                            نسيت الباسورد
                        </button>
                    </div>

                </form>

            </div>
            <div class="col-7 col-md-6">
                <img src="{{ asset('frontend/images/login.svg') }}" alt="logo">
            </div>
        </div>
        <!-- </div> -->
    </section>
</body>

</html>