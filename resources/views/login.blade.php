@extends('layout.header')
@section('content')

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

