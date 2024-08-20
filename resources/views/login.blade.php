<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>هيئة التفتيش - تسجيل الدخول </title>
    <link rel="stylesheet" href="{{ asset('frontend/styles/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/styles/login-styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/styles/login-responsive.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container pt-5 pb-5">
        <div class="row col-12 pt-5">
            <div class="col-md-4 col-sm-2">
                <img src="{{ asset('frontend/images/logo.svg') }}" alt="logo" class="logo">
            </div>
            <div class="col-md-8 col-sm-12 col-12">
                <h5 class="login-h5">وزارة الداخلــــــــــــــــــية</h5>
                <p class="login-p">الادارة العامة لشئون التفتيش</p>
                <h2 class="login-h2">المطــــور</h2>
            </div>
        </div>
        <div class="row col-12 d-flex justify-content-between">
            <div class="col-5 col-md-5 d-block">
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
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
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <label for="username" class="login-label">رقم العسكري</label> <br>
                    <input type="text" name="military_number" id="username" class="login-input"> <br>
                    <label for="password" class="login-label">كلمة المرور</label> <br>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="login-input">
                        <label class="toggle-password" onclick="togglePasswordVisibility()">
                            <i id="toggleIcon" class="fa fa-eye"></i>
                        </label>
                    </div>
                    <a href="{{ route('forget_password') }}" class="forget-pass-a" style="text-decoration:underline !important;">نسيت كلمة المرور؟</a>
                    <div class="btns">
                        <button class="btn1" type="submit">تسجيل دخول</button>
                    </div>
                </form>
            </div>
            <div class="col-7 col-md-6">
                <img src="{{ asset('frontend/images/login.svg') }}" alt="background" class="background">
            </div>
        </div>
    </div>

    <!-- Include JS files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById('password');
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
</body>
</html>
