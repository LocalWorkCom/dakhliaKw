@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('content')
@section('title')
    مــهام التــفتــيش
@endsection

@section('content')
    <section>
        <div class="row ">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> مــهام التــفتــيش </p>

                    <div class="d-flex ">
                        <button class="btn-all px-3 " style="color: #FFFFFF; background-color: #274373;"
                            onclick="window.print()">
                            <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
                        </button>
                        <div class="colors  d-flex mx-5">

                            <div class="night rounded p-1 px-3 mx-1"> ليل</div>
                            <div class="morning  rounded p-1 px-3 mx-1 ">صبح</div>
                            <div class="afternoon rounded p-1 px-3 mx-1">عصر</div>
                            <div class="dayoff rounded p-1 px-3 mx-1">راحه</div>
                            <div class="rest rounded p-1 px-3 mx-1">اجازات</div>
                            <div class=" guide rounded p-1 px-3 mx-1">: للارشاد </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <br>
        <div class="container col-12 pb-5 pt-4">


            <div class="  col-12 table-responsive table-container" id="days-table">
                <table class="table table-bordered " id="days-table" dir="rtl" id="days-table">
                    <table border="1" dir="rtl" style="text-align: center;">
                        <thead>
                            <tr>
                                <th scope="col" rowspan="2" style="background-color: #a5d0ffbd;">العدد</th>
                                <th scope="col" rowspan="2" style="background-color: #a5d0ffbd;">العدد</th>
                                <th scope="col" rowspan="2" style="background-color: #e4f1ffbd;">العاصمة+حولي</th>
                                <th scope="col" class="night">الخميس</th>
                                <th scope="col" class="morning">الجمعة</th>
                                <th scope="col" class="morning">السبت</th>
                                <th scope="col" class="dayoff">الأحد</th>
                                <th scope="col" class="dayoff">الأثنين</th>
                                <th scope="col" class="night">الثلاثاء</th>
                                <th scope="col" class="night">الأربعاء</th>
                                <th scope="col" class="morning">الخميس</th>
                                <th scope="col" class="morning">الجمعة</th>
                                <th scope="col" class="dayoff">السبت</th>
                                <th scope="col" class="dayoff">الأحد</th>
                                <th scope="col" class="night">الأثنين</th>
                                <th scope="col" class="night">الثلاثاء</th>
                                <th scope="col" class="afternoon">الأربعاء</th>
                                <th scope="col" class="afternoon">الخميس</th>
                                <th scope="col" class="night">الجمعة</th>
                                <th scope="col" class="night">السبت</th>
                                <th scope="col" class="morning">الأحد</th>
                                <th scope="col" class="morning">الأثنين</th>
                                <th scope="col" class="afternoon">الثلاثاء</th>
                                <th scope="col" class="afternoon">الأربعاء</th>
                                <th scope="col" class="dayoff">الخميس</th>
                                <th scope="col" class="dayoff">الجمعة</th>
                                <th scope="col" class="night">السبت</th>
                                <th scope="col" class="night">الأحد</th>
                                <th scope="col" class="morning">الأثنين</th>
                                <th scope="col" class="morning">الثلاثاء</th>
                                <th scope="col" class="afternoon">الأربعاء</th>
                                <th scope="col" class="afternoon">الخميس</th>
                                <th scope="col" class="night">الجمعة</th>
                                <th scope="col" class="night">السبت</th>
                            </tr>
                            <tr>
                                <th scope="col" class="night">1</th>
                                <th scope="col" class="morning">2</th>
                                <th scope="col" class="morning">3</th>
                                <th scope="col" class="dayoff">4</th>
                                <th scope="col" class="dayoff">5</th>
                                <th scope="col" class="night">6</th>
                                <th scope="col" class="night">7</th>
                                <th scope="col" class="morning">8</th>
                                <th scope="col" class="morning">9</th>
                                <th scope="col" class="dayoff">10</th>
                                <th scope="col" class="dayoff">11</th>
                                <th scope="col" class="night">12</th>
                                <th scope="col" class="night">13</th>
                                <th scope="col" class="afternoon">14</th>
                                <th scope="col" class="afternoon">15</th>
                                <th scope="col" class="night">16</th>
                                <th scope="col" class="night">17</th>
                                <th scope="col" class="morning">18</th>
                                <th scope="col" class="morning">19</th>
                                <th scope="col" class="afternoon">20</th>
                                <th scope="col" class="afternoon">21</th>
                                <th scope="col" class="dayoff">22</th>
                                <th scope="col" class="dayoff">23</th>
                                <th scope="col" class="night">24</th>
                                <th scope="col" class="night">25</th>
                                <th scope="col" class="morning">26</th>
                                <th scope="col" class="morning">27</th>
                                <th scope="col" class="afternoon">28</th>
                                <th scope="col" class="afternoon">29</th>
                                <th scope="col" class="night">30</th>
                                <th scope="col" class="night">31</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="group-table">
                                <td colspan="34">نوبة 1</td>
                            </tr>

                            <tr>
                                <td>1</td>
                                <td>1</td>
                                <td>احمد</td>
                                <td class="rest">طارئه</td>
                                <td class="rest">طارئه</td>
                                <td>عاصمة 2</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>
                                    <ul>
                                        <li>حولي 2</li>
                                        <li>حولي 1</li>

                                    </ul>
                                </td>
                                <td>عالصمه 2</td>
                                <td>حولي</td>
                                <td></td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 1</td>
                                <td>1 حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمة</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولي</td>
                                <td>1حولي</td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>2</td>
                                <td></td>
                                <td>عاصمه3 مع الياسين</td>
                                <td>عاصمه33</td>
                                <td>
                                    <ul>
                                        <li>u222</li>
                                        <li>u222</li>
                                    </ul>
                                </td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 2</td>
                                <td>عالصمه 2</td>
                                <td>حولي</td>
                                <td></td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 1</td>
                                <td>1 حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمة</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولي</td>
                                <td>1حولي</td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>3</td>
                                <td></td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="dayoff"></td>
                                <td>حولى 2</td>
                                <td>عالصمه 2</td>
                                <td>حولي</td>
                                <td></td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 1</td>
                                <td>1 حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمة</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولي</td>
                                <td>1حولي</td>
                            </tr>
                            <tr class="group-table">
                                <td colspan="34">نوبة 3</td>
                            </tr>

                            <tr>
                                <td>1</td>
                                <td>1</td>
                                <td> محمدد</td>
                                <td class="rest">طارئه</td>
                                <td class="rest">طارئه</td>
                                <td>عاصمة 2</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 2</td>
                                <td>عالصمه 2</td>
                                <td>حولي</td>
                                <td></td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 1</td>
                                <td>1 حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمة</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولي</td>
                                <td>1حولي</td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>2</td>
                                <td></td>
                                <td>عاصمه3 مع الياسين</td>
                                <td>عاصمه33</td>
                                <td>عاصمة 2</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 2</td>
                                <td>عالصمه 2</td>
                                <td>حولي</td>
                                <td></td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 1</td>
                                <td>1 حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمة</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولي</td>
                                <td>1حولي</td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>3</td>
                                <td></td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="dayoff"></td>
                                <td>حولى 2</td>
                                <td>عالصمه 2</td>
                                <td>حولي</td>
                                <td></td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 1</td>
                                <td>1 حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمة</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولي</td>
                                <td>1حولي</td>
                            </tr>

                            <tr class="group-table">
                                <td colspan="34">نوبة 2</td>
                            </tr>

                            <tr>
                                <td>1</td>
                                <td>1</td>
                                <td></td>
                                <td class="rest">طارئه</td>
                                <td class="rest">طارئه</td>
                                <td>عاصمة 2</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 2</td>
                                <td>عالصمه 2</td>
                                <td>حولي</td>
                                <td></td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 1</td>
                                <td>1 حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمة</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولي</td>
                                <td>1حولي</td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>2</td>
                                <td></td>
                                <td>عاصمه3 مع الياسين</td>
                                <td>عاصمه33</td>
                                <td>عاصمة 2</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 2</td>
                                <td>عالصمه 2</td>
                                <td>حولي</td>
                                <td></td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 1</td>
                                <td>1 حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمة</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولي</td>
                                <td>1حولي</td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>3</td>
                                <td></td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="rest emergency-text"> مباشرة عمل</td>
                                <td class="dayoff"></td>
                                <td>حولى 2</td>
                                <td>عالصمه 2</td>
                                <td>حولي</td>
                                <td></td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>حولى 1</td>
                                <td>1 حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمة</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td class="dayoff"></td>
                                <td class="dayoff"></td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولى</td>
                                <td>حولى</td>
                                <td>عاصمه</td>
                                <td>عاصمه</td>
                                <td>حولي</td>
                                <td>1حولي</td>
                            </tr>
                        </tbody>


                    </table>

            </div>
        </div>
    </section>
@endsection
