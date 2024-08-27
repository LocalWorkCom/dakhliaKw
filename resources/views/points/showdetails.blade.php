@extends('layout.main')

@push('style')
@endpush
@section('title')
    التفاصيل
@endsection
@section('content')
<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('points.index') }}"> نقاط الوزاره </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a> تفاصيل</a></li>
            </ol>
        </nav>
</div>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> نقاط الوزاره </p>
        </div>
    </div>
    <br>

    <section style="direction: rtl;">
        <div class="row">
            <div class="container c col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11 pt-5 pb-4 px-3">
                <table class="table table-bordered ">
                    <tbody>
                        <tr style="background-color:#f5f6fa;">
                            <th scope="row"> أسم نقطه الوزاره </th>
                            <td>{{ $data->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row"> القطاع الخاص بها</th>
                            <td>
                                {{ $data->sector->name }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">المحافظه الخاصه بها</th>
                            <td>
                                {{ $data->government->name }}
                            </td>
                        </tr>


                        <tr>
                            <th scope="row">المنطقه الخاصه بها</th>
                            <td>
                                {{ $data->region->name }}

                            </td>
                        </tr>
                        <tr>
                            <th scope="row">نظام عمل النقطه</th>
                            <td>
                                {{ $data->work_type == 0 ? 'دوام 24 ساعه' : 'دوام جزئى' }}

                            </td>
                        </tr>
                        @if ($days)
                            @foreach ($days as $day)
                                <tr>
                                    <th scope="row">نظام عمل النقطه</th>

                                    <td>
                                        @if ($day->name == 0)
                                            السبت
                                        @elseif($day->name == 1)
                                            الأحد
                                        @elseif($day->name == 2)
                                            الأثنين
                                        @elseif($day->name == 3)
                                            الثلاثاء
                                        @elseif($day->name == 4)
                                            الأربعاء
                                        @elseif($day->name == 5)
                                            الخميس
                                        @elseif($day->name == 6)
                                            الجمعه
                                        @endif

                                        , "موعد بدايه العمل " : -
                                        {{ formatTime($day->from) }}
                                        - "موعد نهايه العمل " : -
                                        {{ formatTime($day->to) }}

                                    </td>

                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <th scope="row"> رابط جوجل ماب </th>
                            <td>
                                @if ($data->google_map)
                                    <a href="{{ $data->google_map }}" target="_blank">عرض الموقع</a>
                                @else
                                لا يوجد موقع للنقطه
                                @endif

                            </td>
                        </tr>

                        <tr>
                            <th scope="row">الملاحظات</th>
                            <td>
                                {{ $data->note ? $data->note : 'لا يوجد ملاحظات' }}
                            </td>
                        </tr>


                    </tbody>
                    <tfoot>

                    </tfoot>

                </table>

            </div>


    </section>
@endsection

@push('scripts')
@endpush
