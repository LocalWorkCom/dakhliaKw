<?php
@session_start();
@session_destroy();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>هيئة التفتيش - كود التأكيد </title>
    <!-- Cairo Font -->
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
    <link rel="stylesheet" href="{{ asset('frontend/styles/login-styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/styles/login-responsive.css') }}" />
</head>

<body>

    <div class="container pt-5 pb-5">
        <div class="row col-12 pt-5">
            <div class=" col-md-4 col-sm-2">
                <img src="{{ asset('frontend/images/logo.svg') }}" alt="logo" class="logo">
            </div>
            <div class=" col-md-8 col-sm-12 col-12">
                <h5 class="login-h5">وزارة الداخلــــــــــــــــــية</h5>
                <p class="login-p">الادارة العامة لشئون التفتيش</p>
                <h2 class="login-h2">المطــــور</h2>
            </div>
        </div>
        <div class=" row col-12 d-flex justify-content-between">
            <div class="col-5 col-md-5 d-block reset-pass">

                <form action="{{ route('verfication_code') }}" method="post">
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

                    <input type="hidden" name="code" value="{{ $code }}">
                    <input type="hidden" name="military_number" value="{{ $military_number }}">
                    <input type="hidden" name="password" value="{{ $password }}">
                    <h4> ادخل كود التفعيل</h1>
                        <p>تم ارسال كود لرقم الهاتف المسجل وهو :</p>
                        @php
                            $user = App\Models\User::where('military_number', $military_number)->first();
                        @endphp

                        <h6>{{ $user->phone }}</h6>


                        {{-- 
                        <div class="row col-12 d-flex">
                          <form class="code-input d-flex justify-content-center" id="codeInputForm" dir="ltr">
                              <input type="text" maxlength="1" size="1" id="digit1" onkeyup="moveToNext(this, 'digit2')">
                              <input type="text" maxlength="1" size="1" id="digit2" onkeyup="moveToNext(this, 'digit3')">
                              <input type="text" maxlength="1" size="1" id="digit3" onkeyup="moveToNext(this, 'digit4')">
                              <input type="text" maxlength="1" size="1" id="digit4" onkeyup="combineDigits()">
                              <input type="hidden" name="verfication_code" id="username" class="login-input">
                          </form>
                      </div> --}}

                        <input type="text" name="verfication_code" id="username" class="login-input"><br>

                        <div class="btns  ">
                            <button class="btn1" type="submit"> تأكيد </button> &nbsp; &nbsp; &nbsp;
                        </div>
                </form>

                <div class="resend d-flex">
                    <p>لم يتم ارسال الكود؟</p>
                    {{-- <form action="{{ route('resend_code') }}" method="POST">
                        @csrf
                        <input type="hidden" name="military_number" value="{{ $military_number }}">
                        <input type="hidden" name="password" value="{{ $password }}">
                        <button class="btn2" type="submit">إعادة الإرسال</button>
                    </form> --}}
                 
                    <form id="resendForm" action="{{ route('resend_code') }}" method="POST">
                              @csrf
                              <input type="hidden" name="military_number" value="{{ $military_number }}">
                              <input type="hidden" name="password" value="{{ $password }}">
                              <a href="#" class="resend-pass-a" onclick="document.getElementById('resendForm').submit(); return false;">
                                  إعادة إرسال
                              </a>
                          </form>

                </div>
            </div>
            <div class="col-7 col-md-6">
                <img src="{{ asset('frontend/images/login.svg') }}" alt="background" class="background">
            </div>
        </div>
    </div>


    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"
            integrity="sha512-2rNj2KJ+D8s1ceNasTIex6z4HWyOnEYLVC3FigGOmyQCZc2eBXKgOxQmo3oKLHyfcj53uz4QMsRCWNbLd32Q1g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            function moveToNext(current, nextFieldID) {
                if (current.value.length === 1) {
                    document.getElementById(nextFieldID).focus();
                }
                combineDigits();
            }

            function combineDigits() {
                const digit1 = document.getElementById('digit1').value;
                const digit2 = document.getElementById('digit2').value;
                const digit3 = document.getElementById('digit3').value;
                const digit4 = document.getElementById('digit4').value;

                document.getElementById('username').value = digit1 + digit2 + digit3 + digit4;
            }

            function generateVerificationCode() {
                // Generate a random 4-digit code
                return Math.floor(1000 + Math.random() * 9000);
            }

            function updateCodeDisplay() {
                // Get the code display element
                const codeDisplay = document.getElementById('codeDisplay');
                // Generate a new verification code
                const code = generateVerificationCode();
                // Display the code
                codeDisplay.textContent = code;
                // Clear input fields
                document.getElementById('digit1').value = '';
                document.getElementById('digit2').value = '';
                document.getElementById('digit3').value = '';
                document.getElementById('digit4').value = '';
                combineDigits();
            }
        </script>
    @endsection
</body>

</html>
