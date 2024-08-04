@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>

@section('content')
@section('title')
    الصلاحيات
@endsection

<div class="row ">
    <div class="container welcome col-11">
        <p> فترة  العمل</p>

    </div>
</div>
<br>

<div class="row">
    <div class="container  col-11 mt-3 p-0 ">
        <div class="row d-flex justify-content-between " dir="rtl">
            <div class="form-group mt-4 mx-3  d-flex">
                <button class="btn-all px-3" style="color: #274373;" data-bs-toggle="modal"
                    data-bs-target="#myModal1">
                    <img src="../images/time.svg" alt="">
                 اضافة فترة
                </button>
            </div>
                <!-- <div class="form-group mt-4 mx-3  d-flex justify-content-end ">
                    <button class="btn-all px-3 " style="color: #FFFFFF; background-color: #274373;"
                        onclick="window.print()">
                        <img src="../images/print.svg" alt=""> طباعة
                    </button>
                </div> -->
            </div>
       
        <div class="col-lg-12" dir="rtl">
            <div class="bg-white ">
                <div>
                    <table id="users-table"
                        class="display table table-responsive-sm  table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>رقم التسلسلي</th>
                                <th>اسم الفترة</th>
                                <th>اوقات العمل </th>
                                <th> عدد ساعات العمل</th>
                                <th style="width:150px !important;">العمليات</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-center">
                                <div class="title d-flex flex-row align-items-center ">
                                    <h5 class="modal-title"> اضافة فترة </h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    &times;
                                </button>
                            </div>
                            <div class="modal-body">
                            <div id="firstModalBody" class="mb-3 mt-3 d-flex justify-content-center">
                                <div class="container" style="border: 0.2px solid rgb(166, 165, 165);">
                                    <div class="form-group mt-4 mb-3">
                                        <label class="d-flex justify-content-start pt-3 pb-2" for="name"> اسم
                                            الفتره</label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            placeholder="مجموعة أ" required>

                                    </div>
                                    <div class="form-group  mb-3">
                                        <label class="d-flex justify-content-start pb-2" for="name"> 
                                              بداية فترة العمل</label>
                                              <input type="time" id="name" name="name" class="form-control"
                                              placeholder=" 4" required>

                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="d-flex justify-content-start pb-2" for="name" > 
                                            نهاية  فترة العمل</label>
                                            <input type="time" id="name" name="name" class="form-control"
                                            placeholder=" 4" required >

                                  </div>
                                    <div class="text-end d-flex justify-content-end mx-2 pb-4 pt-2">
                                        <button type="button" class="btn-all mx-2 p-2" style="background-color: #274373; color: #ffffff;" id="openSecondModalBtn">
                                            <img src="../images/white-add.svg" alt="img"> اضافة
                                        </button>
                                        <button type="submit" class="btn-all p-2" style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);" data-bs-dismiss="modal" aria-label="Close">
                                            <img src="../images/red-close.svg" alt="img"> الغاء
                                        </button>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Second Modal Body (Initially Hidden) -->
                            <div id="secondModalBody" class="d-none">
                                <div class="body-img-modal d-block mb-4">
                                    <img src="../images/ordered.svg" alt="">
                                    <p>تمت الاضافه بنجاح</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

          





<script>
document.addEventListener('DOMContentLoaded', function () {
    // Get elements
    var openSecondModalBtn = document.getElementById('openSecondModalBtn');
    var firstModalBody = document.getElementById('firstModalBody');
    var secondModalBody = document.getElementById('secondModalBody');

    // Add click event listener
    openSecondModalBtn.addEventListener('click', function () {
        // Hide the first modal body
        firstModalBody.classList.add('d-none');

        // Show the second modal body
        secondModalBody.classList.remove('d-none');
    });
});
</script>
@endsection