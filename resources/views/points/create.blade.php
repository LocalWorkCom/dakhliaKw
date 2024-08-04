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
            <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
            <li class="breadcrumb-item"><a href=""># </a></li>
            <li class="breadcrumb-item active" aria-current="page"> <a href=""> #</a></li>
        </ol>
    </nav>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> نقاط الوزاره </p>
    </div>
</div>
<br>
<div class="row" dir="rtl">
    <div class="container moftsh col-11 mt-3 p-0 pb-3 ">
        <h3 class="pt-3  px-md-5 px-3 "> اضافة نقطة </h3>
        <div class="form-row mx-2 mb-2 ">
            <div class="input-group moftsh px-md-5 px-3 pt-3">
                <label class="pb-3" for=""> اسم النقطة </label>
                <input type="text" id="" class="form-control" placeholder=" الجهراء" />

            </div>
        </div>
       
        <div class="form-row mx-2 mb-2 ">
            <div class="input-group moftsh px-md-5 px-3 pt-3">
                <label class="pb-3" for=""> اختر القطاع </label>
                <select name="" id="" style="border: 0.2px solid rgb(199, 196, 196);">
                    <option value="">نقطة </option>
                    <option value="">نقطة</option>
                    <option value="">نقطة</option>
                </select>
            </div>
        </div>
        <div class="form-row mx-2 mb-2 ">
            <div class="input-group moftsh px-md-5 px-3 pt-3">
                <label class="pb-3" for=""> اختر المحافظة </label>
                <select name="" id="" style="border: 0.2px solid rgb(199, 196, 196);">
                    <option value="">نقطة </option>
                    <option value="">نقطة</option>
                    <option value="">نقطة</option>
                </select>
            </div>
        </div>
        <div class="form-row mx-2 mb-2 ">
            <div class="input-group moftsh px-md-5 px-3 pt-3">
                <label class="pb-3" for=""> اختر المنطقة </label>
                <select name="" id="" style="border: 0.2px solid rgb(199, 196, 196);">
                    <option value="">نقطة </option>
                    <option value="">نقطة</option>
                    <option value="">نقطة</option>
                </select>
            </div>
        </div>
        <div class="form-row mx-2 mb-2 ">
            <div class="input-group moftsh px-md-5 px-3 pt-3">
                <label class="pb-3" for=""> رابط جوجل ماب </label>
                <input type="text" id="" class="form-control" placeholder=" ادخل الرابط" />
            </div>
        </div>
        <div class="form-row   mx-2 mb-2 ">
            <div class="input-group moftsh px-md-5 px-3 pt-3 col-6">
                <label class="pb-3" for=""> خطوط العرض </label>
                <input type="text" id="" class="form-control" placeholder=" الجهراء" />
            </div>
            <div class="input-group moftsh px-md-5 px-3 pt-3 col-6">
                <label class="pb-3" for=""> خطوط </label>
                <input type="text" id="" class="form-control" placeholder=" الجهراء" />
            </div>
        </div>
        <div class="form-row mx-2 mb-2 ">
            <div class="input-group moftsh px-md-5 px-3 pt-3">
                <label class="pb-3" for=""> اضف ملاحظتك </label>
                <input type="text" id="" class="form-control note" placeholder="ملاحظتك" />
            </div>
        </div>
        <div class="container col-11 ">
            <div class="form-row d-flex justify-content-end mt-4 mb-3">

                <button type="submit" class="btn-blue"><img src="../images/white-add.svg" alt="img" height="20px"
                        width="20px"> اضافة</button>
            </div>
        </div>
    </div>
@endsection
@push('scripts')


@endpush
