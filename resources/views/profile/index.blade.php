@extends('layout.main')
@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
    </script>
@endpush
@section('title')
    الاعدادات
@endsection
@section('content')
    <div class="row " dir="rtl">
        <div class="container  col-11" style="background-color:transparent;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a href="{{ route('home') }}">الرئيسيه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('profile') }}">صفحة المستخدم </a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p>{{ auth()->user()->name }} مرحبا بك

            </div>
        </div>
    </div>
    <br>
    <div class="row">

        <div class="container d-md-block  col-11 mt-3 pt-3 pb-3 " style=" background-color: #ffffff; ">
            @include('inc.flash')
            <div class=" d-flex justify-content-end mx-3  ">
                <h1>اختر الصلاحيات للظهور في الرئيسية</h1>
            </div>
            <div class=" d-flex justify-content-end mx-3 ">

                <form id="checkboxForm" class="permission" dir="rtl" action="{{ route('profile.store') }}"
                    method="POST">
                    @csrf
                    @foreach ($Statistics as $Statistic)
                        <label class="py-2"><input type="checkbox" name="statistic_id[]" value="{{ $Statistic->id }}"
                                @if (in_array($Statistic->id, $UserStatistic->toArray())) checked @endif>
                            {{ $Statistic->name }}</label><br>
                    @endforeach
                    <div class="text-end">
                        <button type="submit" class="btn-blue">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
