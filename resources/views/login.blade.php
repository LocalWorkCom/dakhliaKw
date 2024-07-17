@extends('layout.header')
@section('content')
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<style>
    .password-container {
        position: relative;
        display: inline-block;
    }

    .login-input {
        padding-right: 30px; /* Space for the eye icon */
    }

    .eye-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
    }

    .alert {
        color: red;
        font-weight: bold;
        margin-top: 10px;
    }
</style>
<section>
    <div class="row col-12 d-flex justify-content-between">
        <div class="col-5 col-md-5">
            <form action="{{ route('login') }}" method="post">
                @csrf
                @if(session('error'))
                    <div class="alert">
                        {{ session('error') }}
                    </div>
                @endif
                <label for="username" class="login-label">اسم المستخدم</label> <br>
                <input type="text" name="military_number" id="username" class="login-input"><br>
                <label for="password" class="login-label">كلمة المرور</label><br>
                <div class="password-container">
                    <input type="password" name="password" id="password" class="login-input"> 
                    <span class="eye-icon" id="togglePassword">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <br>
                <div class="btns d-flex justify-content-between ">
                    <button class="btn1" type="submit">تسجيل دخول</button>  
                    &nbsp; &nbsp; &nbsp;
                    {{-- <button class="btn2"><i class="fa-solid fa-right-from-bracket"></i>  &nbsp; تسجيل خروج</button> --}}
                </div>
                <button class="btn2" type="button" onclick="location.href='{{ route('forget_password') }}'">
                    نسيت الباسورد
                </button>
                
                
            </form>

        </div>
        <div class="col-7 col-md-6">
            <img src="assets/images/home.png" alt="background" class="background">
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM fully loaded and parsed');
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        if (togglePassword) {
            console.log('Toggle button found');
            togglePassword.addEventListener('click', function () {
                const currentType = passwordField.getAttribute('type');
                console.log('Current type:', currentType);
                const type = currentType === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                console.log('New type:', type);

                // Toggle the eye slash icon
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        } else {
            console.log('Toggle button not found');
        }
    });
</script>
@endsection
