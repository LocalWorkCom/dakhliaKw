@extends('layout.header')
@section('content')
<section>
    <div class=" row col-12 d-flex justify-content-between">
        <div class="col-5 col-md-5">
            <form action="{{ route('login') }}" method="post">
                @csrf
                <label for="username" class="login-label">اسم المستخدم</label> <br>
                <input type="text" name="military_number" id="username" class="login-input"><br>
                <label for="password" class="login-label">كلمة المرور</label><br>
                <input type="password" name="password" id="password" class="login-input"> <br>
                <div class="btns d-flex justify-content-between ">
                    <button class="btn1" type="submit"> تسجيل دخول</button>  
                    &nbsp; &nbsp; &nbsp;
                    {{-- <button class="btn2"><i class="fa-solid fa-right-from-bracket"></i>  &nbsp; تسجيل خروج</button> --}}
                </div>
            </form>
          
        </div>
        <div class="col-7 col-md-6">
  <img src="assets/images/home.png" alt="background" class="background">
        </div>
      </div>
    </div>
</section>
    
@endsection


    


  