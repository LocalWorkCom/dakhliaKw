@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('working_trees.list') }}">نظام العمل</a></li>
                <li class="breadcrumb-item" aria-current="page"> <a href=""> اضافة </a></li>
            </ol>
        </nav>
    </div>
    @include('inc.flash')
    <div class="row">
        <div class="container welcome col-11">
            <p> نظام العمل </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container moftsh col-11 mt-3 p-0 pb-3 ">
            <h3 class="pt-3  px-md-5 px-3 "> من فضلك ادخل البيانات </h3>
            <form action="{{ route('workingTree.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh px-md-5 px-3 pt-3">
                        <label class="pb-3" for=""> اسم نظام العمل </label>
                        <input type="text" id="" class="form-control" placeholder=" الجهراء" />
                    </div>
                </div>
                <div class="form-row mx-2 mb-2">
                    <div class="input-group moftsh px-md-5 px-3 pt-3 col-6 holidays">
                        <label class="pb-3" for="holidays">عدد ايام العمل</label>
                        <select name="holidays" id="holidays" style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                    <div class="input-group moftsh px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="work-days">عدد ايام الاجازات </label>
                        <select name="work-days" id="work-days" style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>

                </div>
                <div class="form-row mx-2 mb-2 holidays-1" style="display: none;">
                    <div class="input-group moftsh px-md-5 px-3 pt-3">
                        <label class="pb-3" for="holiday1"> اليوم الاول</label>
                        <select name="holiday1" id="holiday1" style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر الفترة</option>
                            <option value="2-4">2-4</option>
                            <option value="3-5">3-5</option>
                            <option value="4-7">4-7</option>
                        </select>
                    </div>
                </div>
                <div class="form-row mx-2 mb-2 holidays-2" style="display: none;">
                    <div class="input-group moftsh px-md-5 px-3 pt-3">
                        <label class="pb-3" for="holiday2">اليوم الثاني</label>
                        <select name="holiday2" id="holiday2" style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر الفترة</option>
                            <option value="2-4">2-4</option>
                            <option value="3-5">3-5</option>
                            <option value="4-7">4-7</option>
                        </select>
                    </div>
                </div>
                <div class="form-row mx-2 mb-2 holidays-3" style="display: none;">
                    <div class="input-group moftsh px-md-5 px-3 pt-3">
                        <label class="pb-3" for="holiday3">اليوم الثالث</label>
                        <select name="holiday3" id="holiday3" style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر الفترة</option>
                            <option value="2-4">2-4</option>
                            <option value="3-5">3-5</option>
                            <option value="4-7">4-7</option>
                        </select>
                    </div>
                </div>
                <div class="form-row mx-2 mb-2 holidays-4" style="display: none;">
                    <div class="input-group moftsh px-md-5 px-3 pt-3">
                        <label class="pb-3" for="holiday4">اليوم الرابع</label>
                        <select name="holiday4" id="holiday4" style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر الفترة</option>
                            <option value="2-4">2-4</option>
                            <option value="3-5">3-5</option>
                            <option value="4-7">4-7</option>
                        </select>
                    </div>
                </div>

                <div class="container col-11 ">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">

                        <button type="submit" class="btn-blue"><img src="../images/white-add.svg" alt="img"
                                height="20px" width="20px"> اضافة</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const holidaysSelect = document.getElementById('holidays');
                holidaysSelect.addEventListener('change', () => {
                    const selectedValue = parseInt(holidaysSelect.value, 10);
                    const holidaySections = document.querySelectorAll(
                        '.holidays-1, .holidays-2, .holidays-3, .holidays-4');
                    holidaySections.forEach(section => {
                        section.style.display = 'none';
                    });

                    if (selectedValue >= 1) {
                        document.querySelector('.holidays-1').style.display = 'block';
                    }
                    if (selectedValue >= 2) {
                        document.querySelector('.holidays-2').style.display = 'block';
                    }
                    if (selectedValue >= 3) {
                        document.querySelector('.holidays-3').style.display = 'block';
                    }
                    if (selectedValue >= 4) {
                        document.querySelector('.holidays-4').style.display = 'block';
                    }
                });
            });
        </script>
    @endpush
@endsection
