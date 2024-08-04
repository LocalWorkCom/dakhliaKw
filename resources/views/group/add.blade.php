@extends('layout.main')

@push('style')
@endpush

@section('title')
    اضافة
@endsection

@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('group.view') }}">المجموعات</a></li>
                <li class="breadcrumb-item active" aria-current="page">اضافة مجموعة</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="container welcome col-11">
            <p>المجــــــــموعات</p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container col-11 mt-3 p-0">
            @include('inc.flash')
            <form action="{{ route('group.add') }}" method="Any" enctype="multipart/form-data">
                @csrf

                <div class="form-row mx-md-2">
                    <div class="form-group col-md-12">
                        <label for="name">الاسم</label>
                        <input type="text" class="form-control" name="name" id="name1" placeholder="الاسم" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="work_system">نظام العمل</label>
                        <input type="text" class="form-control" name="work_system" id="work_system" placeholder="نظام العمل" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="inspection_points">عدد نقاط التفتيش</label>
                        <input type="number" class="form-control" name="inspection_points" id="inspection_points" placeholder="عدد نقاط التفتيش" required>
                    </div>
                </div>
                <div class="form-row" dir="ltr">
                    <button class="btn-blue mx-md-4" type="submit">اضافة</button>
                </div>
                <br>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
