@extends('layout.header')
@section('content')

    {{-- {{dd($error)}} --}}
    <section>
        <div class="row col-12 d-flex justify-content-between">
            <div class="col-5 col-md-5">
                <form action="{{ route('reset_password') }}" method="post">
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
                    {{-- <input type="hidden" name="military_number" value="{{ old('military_number', $military_number) }}"> --}}
                    <input type="hidden" name="firstlogin" value="{{ $firstlogin }}">
                    <input type="hidden" name="military_number" value="{{ $military_number }}">
                    <label for="username" class="login-label">ادخل كلمه المرور</label> <br>
                    <input type="password" name="password" id="username" class="login-input"><br>

                    <label for="username" class="login-label">تاكيد كلمه المرور</label> <br>
                    <input type="password" name="password_confirm" id="username" class="login-input"><br>
                    <br>
                    <div class="btns d-flex justify-content-between ">
                        <button type="submit">تاكيد </button>
                        &nbsp; &nbsp; &nbsp;
                        {{-- <button class="btn2"><i class="fa-solid fa-right-from-bracket"></i>  &nbsp; تسجيل خروج</button> --}}
                    </div>


                </form>

            </div>
            <div class="col-7 col-md-6">
                <img src="assets/images/home.png" alt="background" class="background">
            </div>
        </div>
    </section>
@endsection
