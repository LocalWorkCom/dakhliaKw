@extends('layout.main')

@push('style')
@endpush
@section('title')
    التفاصيل
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('instant_mission.index') }}">أمر الخدمه</a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a> تفاصيل</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> أوامر الخدمه
            </p>
        </div>
    </div>
    <br>

    <section style="direction: rtl;">
        <div class="row">
            <div class="container c col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11 pt-5 pb-4 px-3">
                <table class="table table-bordered ">
                    <tbody>
                        <tr style="background-color:#f5f6fa;">
                            <th scope="row"> التاريخ </th>
                            <td>{{  $IM->date }}</td>
                        </tr>
                        <tr>
                            <th scope="row"> الاسم</th>
                            <td>
                                {{ $IM->label }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">المجموعة</th>
                            <td>
                                {{ $IM->group->name }}
                            </td>
                        </tr>


                        <tr>
                            <th scope="row">الفريق </th>
                            <td>
                                {{ $IM->group_team_id ? $IM->groupTeam->name :'كل دوريات المجموعه' }}

                            </td>
                        </tr>
                        <tr>
                            <th scope="row">المفتش</th>
                            <td>
                                {{ $IM->inspector_id ? $IM->inspector->name : 'كل مفتشين الدوريه' }}

                            </td>
                        </tr>

                        <tr>
                            <th scope="row"> الموقع</th>
                            <td>
                                    <a href="{{$IM->location  }}" target="_blank">عرض الموقع</a>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">الملاحظات</th>
                            <td>
                                {{  $IM->description ?? 'لا يوجد ملاحظات' }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;">الملفات </th>
                            <td>
                                <div class="row">
                                    <div class="col-md-11 mb-3 px-5 mt-2 d-flex flex-wrap">
                                        @if (!empty($IM->attachment))
                                            {{-- Split the URLs by commas and loop through them --}}
                                            @foreach (explode(',', $IM->attachment) as $image_url)
                                                <div class="pb-4 mx-2">
                                                    {{-- Display the image in a thumbnail --}}
                                                    <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal" data-image="{{ $image_url }}">
                                                        <img src="{{ $image_url }}" class="img-thumbnail mx-2" alt="Image Preview">
                                                        <br><br>

                                                        {{-- Add download button if the user has permission --}}
                                                        @if (Auth::user()->hasPermission('download outgoing_files'))
                                                            <a id="downloadButton" href="{{ $image_url }}" download class="btn-download">
                                                                <i class="fa fa-download" style="color:green;"></i> تحميل الملف
                                                            </a>
                                                        @endif
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            لا يوجد صور لهذا الصادر
                                        @endif
                                    </div>
                                </div>
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
