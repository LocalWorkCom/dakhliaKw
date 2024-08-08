@extends('layout.main')
@push('style')
@endpush
@section('title')
    القطاعات
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('points.index') }}">النقاط</a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a>أنشاء مجموعه للنقاط</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> النقــــاط  </p>
        </div>
    </div>
    <br>
    <div class="row" dir="rtl">
        <div class="container moftsh col-11 mt-3 p-0 pb-3 ">
            <h3 class="pt-3  px-md-5 px-3 "> من فضلك ادخل البيانات </h3>
            <div class="form-row mx-2 mb-2 ">
                <div class="input-group moftsh px-md-5 px-3 pt-3">
                    <label class="pb-3" for=""> اسم المجموعه </label>
                    <input type="text" id="" class="form-control" placeholder=" اسم المجموعه" />
                </div>
            </div>

            <div class="form-row mx-2 mb-2">
                <div class="input-group moftsh px-md-5 px-3 pt-3">
                    <label class="pb-3" for="holidays">أختر المحافظه الخاصه لمجوعه النقاط</label>
                    <select name="holidays" id="holidays" style="border: 0.2px solid rgb(199, 196, 196);">
                        <option value="" selected disabled>اختر</option>
                        @foreach (getgovernments() as $government)
                        <option value="{{ $government->id }}">{{ $government->name }}</option>
                        @endforeach
                       
                    </select>
                </div>
                

            </div>

           

            <div class="container col-11 ">
                <div class="form-row d-flex justify-content-end mt-4 mb-3">

                    <button type="submit" class="btn-blue"><img src="../images/white-add.svg" alt="img"
                            height="20px" width="20px"> اضافة</button>
                </div>
            </div>
        </div>
    @endsection
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
