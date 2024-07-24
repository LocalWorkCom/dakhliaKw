<div class="all-nav">
    <div class="upper-navbar d-flex">
        <div class="second-section d-flex  col-md-10 col-sm-6">
            <div class="dropdown">
                
                <?php
                $user = auth()->user();
                ?>

                <?php if(!empty($user)): ?>
                <button class="btn btn-2 mt-3" onclick="toggleDropdown()">
                    <i class="fa-solid fa-angle-down mx-2"></i>
                    <?php echo e($user->name); ?>

                    <i class="fa-solid fa-user mx-2"></i>
                </button>
                <div id="dropdownMenu" class="dropdown-menu">
                    <a href="<?php echo e(route('logout')); ?>">تسجيل خروج <i class="fa-solid fa-right-from-bracket"></i></a>
                </div>
                <?php else: ?>
                <button class="btn btn-2 mt-3">
                    <a href="<?php echo e(route('login')); ?>" style="color: #ffffff; text-decoration:none;">سجل الدخول <i
                            class="fa-solid fa-user mx-2"></i></a>
                </button>

                <?php endif; ?>



            </div>
            <button class="btn2 btn-2 mx-5" style="    border-inline: 1px solid rgb(41, 41, 41); height: 100%;"
                onclick="toggleDropdown2()">
                <a class="bell mx-5">
                    <i class=" fa-regular fa-bell"></i>
                </a>
            </button>
            <div id="dropdownMenu2" class="dropdown-menu2">
                <p>notification notification notification notification </p>
                <hr>
                <p>notification notification notification notification </p>
                <hr>
                <p>notification notification notification notification </p>
                <hr>
                <p>notification notification notification notification </p>
                <hr>
                <p>notification notification notification notification </p>
                <hr>
            </div>
            <div class="input-group mx-2">
                <button type="button" class="btn  mt-4" data-mdb-ripple-init>
                    <i class="fas fa-search"></i>
                </button>
                <div class="form-outline  mt-4">
                    <input type="search" id="" class="form-control" placeholder="بحث" />
                </div>
                <select name="#" id="#" class=" mt-4">
                    <option value="#"> المستخدميين </option>
                    <option value="<?php echo e(route('departments.index')); ?>"> الادارات </option>
                    
                    <option value="#"> الموظفين </option>
                    <option value="<?php echo e(route('Export.index')); ?>"> الصادر </option>
                    <option value="#"> الوارد </option>
                </select>
            </div>
        </div>
        <div class="first-section d-flex mt-1 ">
            <h2> الرقابة والتفتيش</h2>
            <img class="mt-2" src="<?php echo e(asset('frontend/images/logo.svg')); ?>" alt="">
        </div>
    </div>
    <div class="navbar navbar-expand-md mb-4 w-100" role="navigation" dir="rtl">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="<?php echo e(route('home')); ?>">
                        <img src="<?php echo e(asset('frontend/images/home.svg')); ?>" alt="logo">
                        <h6>الرئيسية</h6>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('user.index',0)); ?>">
                        <img src="<?php echo e(asset('frontend/images/users.svg')); ?>" alt="logo">
                        <h6>المستخدمين</h6>
                    </a>
                </li>

                <li class="nav-item btn3">
                    <a href="#">
                        <img src="<?php echo e(asset('frontend/images/employees.svg')); ?>" alt="logo">
                        <h6 class=" btn3" onclick="toggleDropdown3()"> الموظفين <i class="fa-solid fa-angle-down"></i>
                        </h6>
                    </a>

                </li>
                <div id="dropdownMenu3" class="dropdown-menu3">
                    <ul>
                        <li style="list-style: inside;">
                            <a href="<?php echo e(route('user.employees', 1)); ?>">الموظفين</a>
                        </li>
                        <li style="list-style: inside;">
                            <a href="<?php echo e(route('vacations.list')); ?>">الاجازات</a>
                        </li>
                        <li style="list-style: inside;">
                            <a href="#">الشيفتات</a>
                        </li>
                        <li style="list-style: inside;">
                            <a href="#">الاجازات</a>
                        </li>
                    </ul>

                </div>


                <li class="nav-item">
                    <a href="<?php echo e(route('sub_departments.index')); ?>">
                        <img src="<?php echo e(asset('frontend/images/departments.svg')); ?>" alt="logo">
                        <h6>الاقسام</h6>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('departments.index')); ?>">
                        <img src="<?php echo e(asset('frontend/images/managements.svg')); ?>" alt="logo">
                        <h6>الادارات</h6>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#">
                        <img src="<?php echo e(asset('frontend/images/settings.svg')); ?>" alt="logo">
                        <h6 class=" btn4" onclick="toggleDropdown4()"> الاعدادات <i class="fa-solid fa-angle-down"></i>
                        </h6>
                    </a>
                </li>
                <div id="dropdownMenu4" class="dropdown-menu4">

                    <ul>
                        <li style="list-style: inside;">
                            <a href="<?php echo e(route('grads.index')); ?>">الرتب العسكريه</a>
                        </li>
                        <li style="list-style: inside;">
                            <a href="<?php echo e(route('job.index')); ?>">الوظائف</a>
                        </li>
                        <li style="list-style: inside;">
                            <a href="<?php echo e(route('government.all')); ?>">المحافظات</a>
                        </li>
                        <li style="list-style: inside;">
                            <a href="<?php echo e(route('vacationType.index')); ?>">أنواع الأجازات</a>
                        </li>
                        <li style="list-style: inside;">
                            <a href="<?php echo e(route('rule.index')); ?>">المهام</a>
                        </li>
                        <li style="list-style: inside;">
                            <a href="<?php echo e(route('permission.index')); ?>">الصلاحيات</a>
                        </li>
                    </ul>
                </div>
    

        <li class="nav-item">

            <a href="<?php echo e(route('iotelegrams.list')); ?>">
                <img src="<?php echo e(asset('frontend/images/exports.svg')); ?>" alt="logo">
                <h6>الوارد</h6>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo e(route('Export.index')); ?>">
                <img src="<?php echo e(asset('frontend/images/imports.svg')); ?>" alt="logo">
                <h6>الصادر</h6>

            </a>
        </li>




        </ul>
    </div>
</div>
</div><?php /**PATH C:\xampp\htdocs\dakhliaKw\resources\views/layout/header.blade.php ENDPATH**/ ?>