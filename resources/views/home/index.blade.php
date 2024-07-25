@extends('layout.main')
@section('title')
الرئيسيه
@endsection
@section('content')

<div class="row ">
    <div class="container welcome col-11">
        <p> {{auth()->user()->name}} مرحـــــــــــــــبا بك </p>
    </div>

</div>
<br>
<div class="row">
    <div class="container container-not-responsive col-11 mt-3 p-0 col-md-11 col-lg-3 col-s-11">
        <div class="header-side d-flex">
            <p>المهام</p>
            <img src="{{ asset('frontend/images/tasks.svg')}}" alt="">
        </div> <br>
        <div class="progress blue mt-3">
            <span class="progress-left">
                <span class="progress-bar"></span>
            </span>
            <span class="progress-right">
                <span class="progress-bar"></span>
            </span>
            <div class="progress-value">28/30</div>
        </div>
        <div class="footer-side">
        <div class="name">
            <p>تجديد هوية</p>
            <h3>بشير سالم</h3>
            <button class="order">ordered</button>
            <img class="ordered-img" src="{{ asset('frontend/images/ordered.svg') }}" alt="img">


          </div>
     </div>

    </div>
    <div class=" container  col-11 mt-3 col-lg-7 col-md-11 col-s-11 ">
        <div class=" col-12  col-md-11 col-sm-12 d-md-flex   my-4">
            <div class=" card2 col-md-6 col-12 d-flex col-sm-10 mx-lg-3 mx-md-2 sm-mb-2"
                style="background-color:#DCFCE7;">
                <div class="details">
                    <p>الادارات</p>
                    <p>{{$depCount}}</p>
                </div>
                <div class="icon m-5" style="background-color: #0D992C;">
                    <img src="{{ asset('frontend/images/management-card.svg')}}" alt="">
                </div>
            </div>
            <div class=" card1 col-md-6 col-12 d-flex col-sm-10 d-flex mx-lg-3 mx-md-2 sm-mb-2"
                style="background-color:#FFF4DE;">
                <div class="details">
                    <p>الموظفين</p>
                    <p>{{$empCount}}</p>
                </div>

                <div class="icon m-5" style="background-color: #E49500;">
                    <img src="{{ asset('frontend/images/employees-card.svg')}}" alt="">
                </div>
            </div>

        </div>
        <div class="col-12 col-md-11 col-sm-12 d-md-flex ">
            <div class="card3 col-md-6 col-12 d-flex col-sm-10 d-flex mx-lg-3 mx-md-2 sm-mb-2"
                style="background-color:#F3E8FF;">
                <div class="details">
                    <p>الصادر</p>
                    <p>{{$outCount}}</p>
                </div>

                <div class="icon m-5" style="background-color: #A900E4;">
                    <img src="{{ asset('frontend/images/imports-card.svg')}}" alt="">
                </div>
            </div>
            <div class="card4 col-md-6 col-12 d-flex col-sm-10 d-flex mx-lg-3 mx-md-2 mb-5 sm-mb-2"
                style="background-color:#E8F0FF;">
                <div class="details ">
                    <p>الوارد</p>
                    <p>{{$ioCount}}</p>
                </div>

                <div class="icon m-5" style="background-color: #005BE4;">
                    <img src="{{ asset('frontend/images/exports-card.svg')}}" alt="">
                </div>
            </div>
        </div>
    </div>
    <div class="container container-responsive col-xs-11 col-11 mt-4 ">
      <div class="header-side d-flex">
       <p>المهام</p>
       <img src="/images/tasks.svg" alt="">
      </div> <br>

<div class="resp d-flex justify-content-between align-items-center" dir="rtl">
  <div class="progress blue mt-3">
       <span class="progress-left">
         <span class="progress-bar"></span>
       </span>
       <span class="progress-right">
           <span class="progress-bar"></span>
       </span>
       <div class="progress-value">28/30</div>
   </div>
      <div class="footer-side">
       <div class="name">
         <p>تجديد هوية</p>
         <h3>بشير سالم</h3>
         <button class="order">
           ordered
         </button>
        </div>
      </div>
</div>

     </div>
</div>


</div>

@endsection