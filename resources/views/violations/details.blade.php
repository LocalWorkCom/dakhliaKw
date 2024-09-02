@extends('layout.main')
@section('title')
عرض
@endsection
@section('content')
<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('viollation') }}">سجل المخالفات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> تفاصيل </a></li>
            </ol>
        </nav>
    </div>
</div>
    <div class="row ">
        <div class="container welcome col-11">
            <p>    {{$title}}</p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row " dir="rtl">

            </div>
           
            <div class="form-row mx-2 pt-5 pb-4">
            @if($type==0)
                <table class="table table-bordered" dir="rtl">
                  
                    <tbody>
                        <tr>
                            <th>النقطة :</th>
                            <td>{{$data->point->name}}</td>
                        </tr>
                        <tr>
                            <th scope="row" >العددا لكلى</th>
                            <td >{{ $data->total_number}}</td>
                        </tr>

                        <tr>
                            <th scope="row">العدد الفعلى</th>
                            <td>{{ $data->actual_number }}</td>
                        </tr>
                        <tr>
                            <th scope="row"> العجز</th>
                            <td>{{  $data->total_number- $data->actual_number  }}</td>
                        </tr>
                        </tbody>
                        </table>
                       @foreach($details as $detail)
                       <table class="table table-bordered" dir="rtl">
                       <thead>
                        <tr><th colspan="2">سجل العجز</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>الأسم :</th>
                            <td>{{$detail->grade_id?$detail->grade->name."/" : " "}}{{$detail->name}}</td>
                        </tr>
                        @if($detail->military_number)
                        <tr>
                            <th scope="row"> الرقم العسكرى</th>
                            <td>{{ $detail->military_number }}</td>
                        </tr>
                        @endif
                        @if($detail->civil_number)
                        <tr>
                            <th scope="row"> الرقم المدني</th>
                            <td>{{ $detail->civil_number }}</td>
                        </tr>
                        @endif
                       
                        <tr>
                            <th scope="row"> نوع الغياب</th>
                            <td>{{  $detail->absenceType->name  }}</td>
                        </tr>
                        </tbody>
                        </table>
                       @endforeach
                  
                @else
                <table class="table table-bordered" dir="rtl">
                    <tbody>
                    <tr>
                            <th>الأسم :</th>
                            <td>{{$data->grade_id?$detail->grade->name."/" : " "}}{{$data->name}}</td>
                        </tr>
                        @if($data->military_number)
                        <tr>
                            <th scope="row"> الرقم العسكرى</th>
                            <td>{{ $data->military_number }}</td>
                        </tr>
                        @endif
                        @if($data->civil_number)
                        <tr>
                            <th scope="row"> الرقم المدني</th>
                            <td>{{ $data->civil_number }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th scope="row"> المخالفات</th>
                            <td>{{  $data->ViolationType  }}</td>
                        </tr>
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
@endsection
