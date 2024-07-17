@extends('layout.header')
@section('content')

  <div class="row ">
      <div class="container welcome col-11">
          <p> مرحـــــــــــــــبا بك </p>
      </div>
  </div>
  <br>
  <div class="row">
      <div class="container col-3 mt-3 p-0">
          <div class="header-side d-flex">
              <p>المهام</p>
              <img src="../images/tasks.svg" alt="">
          </div> <br>
          <div class="progress blue mt-5">
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
      <div class=" container col-7 mt-3">

          <div class="col-12 d-flex mx-4  my-4">
              <div class="card2 col-5 d-flex mx-3">
                  <div class="details">
                      <p>الادارات</p>
                      <p>25212</p>
                  </div>
                  <div class="icon m-5" style="background-color: #0D992C;">
                      <img src="../images/management-card.svg" alt="">
                  </div>
              </div>
              <div class=" card1 col-5 d-flex mx-5">
                  <div class="details">
                      <p>الموظفين</p>
                      <p>98224</p>
                  </div>

                  <div class="icon m-5" style="background-color: #E49500;">
                      <img src="../images/employees-card.svg" alt="">
                  </div>
              </div>

          </div>
          <div class="col-12 d-flex mx-4">
              <div class="card3 col-5 d-flex mx-4 mb-2">
                  <div class="details">
                      <p>الصادر</p>
                      <p>65423</p>
                  </div>

                  <div class="icon m-5" style="background-color: #A900E4;">
                      <img src="../images/imports-card.svg" alt="">
                  </div>
              </div>
              <div class="card4 col-5 d-flex mx-4 mb-5">
                  <div class="details ">
                      <p>الوارد</p>
                      <p>21025</p>
                  </div>

                  <div class="icon m-5" style="background-color: #005BE4;">
                      <img src="../images/exports-card.svg" alt="">
                  </div>
              </div>
          </div>
      </div>

  </div>


  </div>

@endsection
    
   