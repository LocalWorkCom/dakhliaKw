@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('content')
<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('working_trees.list') }}">نظام العمل </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="">عرض</a></li>
            </ol>
        </nav>
    </div>
</div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> نظام العمــــل </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 pb-4">
            <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">

                </div>
            </div>

            <div class="form-row mx-2 ">
                <table class="table table-bordered" dir="rtl">
                    <tbody>

                        <tr>
                            <th scope="row"style="background: #f5f6fa;"> اسم النظام:</th>
                            <td colspan="7">{{ $WorkingTree->name }}</td>
                        </tr>

                        {{-- <tr>
                            <th scope="row" style="background: #f5f6fa;"> عدد ايام الاجازات:</th>
                            <td colspan="7">{{ $WorkingTree->holiday_days_num }}</td>
                        </tr> --}}
                        <tr>
                            <th scope="row" style="background: #f5f6fa;"> عدد ايام :</th>
                            <td colspan="7">
                                {{ $WorkingTree->working_days_num+$WorkingTree->holiday_days_num }}
                            </td>
                        </tr>
                        @if (isset($WorkingTree->workingTreeTimes))
                            @foreach ($WorkingTree->workingTreeTimes as $item)
                                <tr>
                                    <th scope="row" style="background: #f5f6fa;">ترتيب اليوم</th>
                                    <th>اليوم {{ $item->day_num }}</th>
                                    @if($item->is_holiday)
                                    <th>
                                        اجازة
                                    </th>
                                    @else
                                    <th scope="row" style="background: #f5f6fa;">اسم الفترة</th>
                                    <td>{{ $item->workingTime->name }}</td>
                                    <th scope="row" style="background: #f5f6fa;">وقت البداية</th>
                                    <td>{{ $item->workingTime->start_time }}</td>
                                    <th scope="row" style="background: #f5f6fa;">وقت النهاية</th>
                                    <td>{{ $item->workingTime->end_time }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif


                    </tbody>
                </table>



            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {

            });
        </script>
    @endpush
@endsection
