@extends('layout.header')
@section('content')


<section>
    <div class="row col-12 d-flex justify-content-between">
        <div class="col-5 col-md-5">
            <form action="{{ route('verfication_code') }}" method="post">
                @csrf
                @if(session('error'))
                    <div class="alert">
                        {{ session('error') }}
                    </div>
                @endif
                <input type="hidden" name="code" value="{{$code}}">
                <input type="hidden" name="military_number" value="{{$military_number}}">
                <label for="username" class="login-label"> ادخل الكود</label> <br>
                <input type="text" name="verfication_code" id="username" class="login-input"><br>
               
                <br>
                <div class="btns d-flex justify-content-between ">
                    <button class="btn1" type="submit">تاكيد </button>  
                    &nbsp; &nbsp; &nbsp;
                    {{-- <button class="btn2"><i class="fa-solid fa-right-from-bracket"></i>  &nbsp; تسجيل خروج</button> --}}
                </div>

                {{-- <div class="btns d-flex justify-content-between ">
                    <button class="btn2" type="button" onclick="location.href='{{ route('resend_code') }}'" name="military_number" value="{{$military_number}}"> اعاده الارسال</button>  
                    &nbsp; &nbsp; &nbsp;
                    <button class="btn2"><i class="fa-solid fa-right-from-bracket"></i>  &nbsp; تسجيل خروج</button>
                </div> --}}
            </form>

            <form action="{{ route('resend_code') }}" method="POST">
                @csrf
                <input type="hidden" name="military_number" value="{{ $military_number }}">
                <button class="btn2" type="submit">إعادة الإرسال</button>
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
