@extends('layout.header')
@section('content')
    <section>
        <div class="row col-12 d-flex justify-content-between">
            <div class="col-5 col-md-5">
                <form action="{{ route('forget_password2') }}" method="post">
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
                    <label for="username" class="login-label">اسم المستخدم</label> <br>
                    <input type="text" name="military_number" id="username" class="login-input"><br>

                    <br>

                    <button class="btn2" type="submit"> ارسال </button>

                </form>

            </div>
            <div class="col-7 col-md-6">
                <img src="assets/images/home.png" alt="background" class="background">
            </div>
        </div>
    </section>
@endsection
