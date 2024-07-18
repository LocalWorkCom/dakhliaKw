<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>
    <script type="application/javascript" src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap-->
    <link href="{{ asset('frontend/styles/bootstrap.min.css')}}" rel="stylesheet" id="bootstrap-css">
   
   
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    @stack('style')
    <link rel="stylesheet" href="{{ asset('frontend/styles/index.css') }}">
</head>

<body >



  
    <div class="all-nav">
        <div class="upper-navbar d-flex">
        <div class="second-section d-flex mx-4 col-md-9 col-sm-6">
       
              <div class="dropdown">
                
                    <button class="btn btn-2  mt-3" onclick="toggleDropdown()"> 
                      <i class="fa-solid fa-angle-down mx-2"></i>
                        اسم المستخدم
                      <i class="fa-solid fa-user mx-2"></i>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu">
                      <a href="#">تسجيل خروج <i class="fa-solid fa-right-from-bracket"></i></a>
                    
                    </div>
              </div>
              <button class="btn2 btn-2 mx-5"  style="    border-inline: 1px solid rgb(41, 41, 41); height: 100%;" onclick="toggleDropdown2()"> 
                  <a class="bell mx-5" >
                      <i class=" fa-regular fa-bell" ></i>
                  </a>
              </button>
                <div id="dropdownMenu2" class="dropdown-menu2">
                    <p>notification notification notification notification </p><hr>
                    <p>notification notification notification notification </p><hr>
                    <p>notification notification notification notification </p><hr>
                    <p>notification notification notification notification </p><hr>
                    <p>notification notification notification notification </p><hr>
                
                </div>
              <div class="input-group">
                  <button type="button" class="btn  mt-4" data-mdb-ripple-init>
                    <i class="fas fa-search"></i>
                  </button>
                  <div class="form-outline  mt-4" >
                    <input type="search" id="" class="form-control" placeholder="بحث"/>
                  </div>
                  <select name="#" id="#" class=" mt-4">
                    <option value="#"> المستخدميين </option>
                    <option value="#"> الادارات </option>
                    <option value="#"> التعيينات </option>
                    <option value="#"> الموظفين </option>
                    <option value="#"> الصادر </option>
                    <option value="#"> الوارد </option>
                  </select>
              </div>
              </div>
              
              <div class="first-section d-flex mt-1 " >
                  <h2>  الرقابة والتفتيش</h2>
                  <img  class="mt-2" src="{{ asset('frontend/images/logo.svg')}}" alt="">
              </div>
      </div>
       
            <div class="navbar navbar-expand-md mb-4 w-100" role="navigation">
    
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarCollapse">
                  <ul class="navbar-nav ml-auto"> 
          
                      <li class="nav-item">
                        <a href="{{ route('iotelegrams.list') }}">
                          <img src="{{ asset('frontend/images/exports.svg')}}" alt="logo">
                          <h6>الوارد</h6>
                        </a>
                      </li>
                      <li class="nav-item">
                          <img src="{{ asset('frontend/images/imports.svg')}}" alt="logo">
                          <h6>الصادر</h6>
                      </li>
                      <li class="nav-item">
                          <img src="{{ asset('frontend/images/employees.svg')}}" alt="logo">
                          <h6>الموظفين</h6>
                      </li>
                      <li class="nav-item">
                          <img src="{{ asset('frontend/images/managements.svg')}}" alt="logo">
                          <h6>التعيينات</h6>
                      </li>
                      <li class="nav-item">
                          <img src="{{ asset('frontend/images/managements.svg')}}" alt="logo">
                          <h6>الادارات</h6>
                      </li>
                      <li class="nav-item">
                          <img src="{{ asset('frontend/images/users.svg')}}" alt="logo">
                          <h6>المستخدمين</h6>
                      </li>
                      <li class="nav-item">
                          <img src="{{ asset('frontend/images/home.svg')}}" alt="logo">
                          <h6>الرئيسية</h6>
                      </li>
          
                  </ul>
          
              </div>
          </div>
          
       
    </div>
       

    <main>
        <div class="row ">
            <div class="container welcome col-11">
        <p > مرحـــــــــــــــبا بك  </p>
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
            <div class=" container col-7 mt-3" >
        
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
                    <img  src="../images/employees-card.svg" alt="">
                    </div>
                     </div>
                    
                 </div>
               <div class="col-12 d-flex mx-4">
                 <div class="card3 col-5 d-flex mx-4 mb-2" >
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
        </main>
        <br> <br> <br> <br> 
        <footer class="my-2">
           <div class="footer ">
            <p>جميع الحقوق محفوظه </p>
           </div>
        </footer>
              <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-zEYs/p5zCUo7LHibzS2KkETvP3L3PaZGvZLme7w+FVZ+Uk2x/E7l3niFf5XFk6ew" crossorigin="anonymous"></script> -->
              <script>
              function toggleDropdown() {
                var dropdownMenu = document.getElementById("dropdownMenu");
                if (dropdownMenu.style.display === "block") {
                  dropdownMenu.style.display = "none";
                } else {
                  dropdownMenu.style.display = "block";
                }
              }
              
              window.onclick = function(event) {
                if (!event.target.matches('.btn')) {
                  var dropdowns = document.getElementsByClassName("dropdown-menu");
                  for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === "block") {
                      openDropdown.style.display = "none";
                    }
                  }
                }
              }
              function toggleDropdown2() {
                var dropdownMenu = document.getElementById("dropdownMenu2");
                if (dropdownMenu.style.display === "block") {
                  dropdownMenu.style.display = "none";
                } else {
                  dropdownMenu.style.display = "block";
                }
              }
              
              window.onclick = function(event) {
                if (!event.target.matches('.btn2')) {
                  var dropdowns = document.getElementsByClassName("dropdown-menu2");
                  for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === "block") {
                      openDropdown.style.display = "none";
                    }
                  }
                }
              }
              </script>
        </body>
        </html>