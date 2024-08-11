@extends('layout.main')

@push('style')

    <style>
        .night {
            background-color: #1F6C97;
            color: #FFFFFF;
        }

        .morning {
            background-color: #E1C35C;
            color: #FFFFFF;
            color: #FFFFFF;
        }

        .dayoff {
            background-color: #B9B5B4;
            color: #FFFFFF;
        }

        .afternoon {
            background-color: #d17404c7;
            color: #FFFFFF;
        }

        .rest {
            background-color: #484848;
            color: #FFFFFF;
        }

        .emergency-text {
            color: rgb(223, 145, 0);
        }
    </style>
@endpush

@section('title')
    المجموعات
@endsection

@section('content')
    <section>
        <div class="row">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> المجــــــــموعات</p>
                    <button class="btn-all px-3" style="color: #274373;" onclick="openAddModal()" data-bs-toggle="modal"
                        data-bs-target="#myModal1">
                        اضافة مجموعة جديده
                        <img src="{{ asset('frontend/images/group-add.svg') }}" alt="">
                    </button>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row d-flex justify-content-between" dir="rtl">
                    <table class="table">
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
                                <!-- <td colspan="1" style="    background-color: #a5d0ffbd;" rowspan="2"> توبة أ</td> -->
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
                            <tr>
                                <td colspan="34">نوبة 1</td>
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
        </div>
    </section>
@endsection
