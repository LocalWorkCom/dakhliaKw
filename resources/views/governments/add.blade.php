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
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('government.all') }}">المحافظات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة محافظه </a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> المحـــافظات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            @include('inc.flash')
            <form action="{{ route('government.add') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row mx-md-2 ">
                    <div class="form-group col-md-12">
                        <label for="name">الاسم</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="الاسم"
                            required>
                    </div>

                </div>
                <div class="form-row" dir="ltr">
                    <button class="btn-blue mx-4" type="submit"> اضافة </button>
                </div>
                <br>
            </form>
        </div>
    </div>
@endsection

@push('scripts')

@endpush
