@extends('layout.main')

@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('inspectors.index') }}">المفتشون </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> تفاصيل </a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> المفتــــــشون </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row " dir="rtl">

            </div>
            <div class="form-row mx-2 pt-5 pb-4">
                <table class="table table-bordered" dir="rtl">
                    <tbody>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;">الاسم</th>
                            <td style="background: #f5f6fa;">{{ $inspector->name }}</td>
                        </tr>

                        <tr>
                            <th scope="row">الرتبه</th>
                            <td>{{ $inspector->position ?: 'لا توجد رتبه لهذا المفتش' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">رقم الهويه</th>
                            <td>{{ $inspector->Id_number ?: 'لا يوجد رقم هويه لهذا المفتش' }}</td>
                        </tr>
                        <tr>
                            <th scope="row"> اسم المجموعه</th>
                            <td>{{ $inspector->group_id ? $inspector->group->name : 'لا يوجد مجموعه لهذا المفتش' }}</td>
                        </tr>
                        <tr>
                            <th scope="row"> الهاتف</th>
                            <td>{{ $inspector->phone ?: 'لا يوجد هاتف لهذا المفتش' }}</td>
                        </tr>
                        <tr>
                            <th scope="row"> نوع المفتش </th>@if ($inspector->type == 'in')
                            <td>مفتش سلوك أنضباطى</td>

                            @elseif ($inspector->type == 'trainee')
                            <td>مفتش متدرب </td>
                            @else
                            <td>مفتش مباني </td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
