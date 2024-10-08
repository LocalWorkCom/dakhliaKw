@extends('layout.main')

@push('style')
@endpush

@section('title')
    تفاصيل
@endsection

@section('content')
<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('group.view') }}">المجموعات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('groupTeam.index', $group_id) }}">الدوريات</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">عرض</a></li>
            </ol>
        </nav>
    </div>
</div>
    <div class="row">
        <div class="container welcome col-11">
            <p>الدوريات</p>
        </div>
    </div>
    <br>
    <section style="direction: rtl;">
        <div class="row">
            <div class="container col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11 pt-4 pb-4  px-3">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row" style="background-color:#f5f6fa;">الاسم</th>
                            <td>{{ $team->name }}</td>
                        </tr>
                        <tr hidden>
                            <th scope="row" style="background-color:#f5f6fa;">امر خدمة</th>
                            <td>{{ $team->service_order ? 'نعم' : 'لا' }}</td>
                        </tr>
                        <tr>
                            <th scope="row" style="background-color:#f5f6fa;">اسم نظام العمل</th>
                            <td>{{ $team->working_tree->name }}</td>
                        </tr>

                        <tr style="background-color:#f5f6fa;">
                            <th colspan="7">المفتشـــــون</th>
                        </tr>
                        @foreach ($inspectors as $inspector)
                            <tr>
                                <td colspan="7">{{ $inspector->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
@endpush
