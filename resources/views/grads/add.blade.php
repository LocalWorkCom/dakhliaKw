@extends('layout.main')

@push('style')
@endpush
@section('title')
    اضافة
@endsection
@section('content')
<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('grads.index') }}">الرتب </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة رتبه </a></li>
            </ol>
        </nav>
    </div>
</div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> الرتـــــب </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            @include('inc.flash')
            <form action="{{ route('grads.add') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group mt-4 mb-3">
                    <label class="d-flex justify-content-start pt-3 pb-2" for="name">ادخل اسم المجموعة</label>
                    <input type="text" id="name" id="nameadd" name="nameadd"class="form-control" placeholder="مجموعة أ" required>
                </div>
                <div class="form-group mb-3">
                    <label class="d-flex justify-content-start pb-2" for="work_system">اختر نظام العمل</label>
                    <select class="w-100 px-2" name="work_system" id="work_system" style="border: 0.2px solid rgb(199, 196, 196);">
                        <option value="">4-2</option>
                        <option value="">5-2</option>
                        <option value="">6-1</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="d-flex justify-content-start pb-2" for="inspection_points">عدد نقاط التفتيش</label>
                    <input type="text" id="inspection_points" name="inspection_points" class="form-control" placeholder="4" required>
                </div>

                <div class="form-row" dir="ltr">
                    <button class="btn-blue mx-md-4" type="submit"> اضافة </button>
                </div>
                <br>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
