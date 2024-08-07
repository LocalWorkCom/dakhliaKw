@extends('layout.main')

@push('style')
@endpush

@section('title')
    تفاصيل
@endsection

@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('group.view') }}">المجموعات</a></li>
                <li class="breadcrumb-item"><a href="{{ route('groupTeam.index', $group_id) }}">الفرق</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">عرض</a></li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="container welcome col-11">
            <p>الفـــرق</p>
        </div>
    </div>
    <br>
    <section style="direction: rtl;">
        <div class="row">
            <div class="container col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11 pt-4 pb-4">
                <table class="table table-bordered ">
                    <tbody>
                        <tr>
                            <th scope="row"  style="background-color:#f5f6fa;">الاسم</th>
                            <td>{{ $team->name }}</td>
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
