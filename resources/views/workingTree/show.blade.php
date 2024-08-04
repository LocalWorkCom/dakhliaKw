@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vacations.list') }}">نظام العمل </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="">عرض</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> نظام العمــــل </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">

                </div>
            </div>



            <div class="form-row mx-2 ">
                <table class="table table-bordered" dir="rtl">
                    <tbody>

                        <tr>
                            <th scope="row"style="background: #f5f6fa;">نوع الاجازة:</th>
                            <td>{{ $vacation->vacation_type->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;">اسم الموظف:</th>
                            <td>
                                {{ $vacation->employee ? $vacation->employee->name : 'عامة' }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;">تاريخ البداية:</th>
                            <td>{{ $vacation->date_from }}</td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;"> تاريخ النهاية:</th>
                            <td>{{ $vacation->date_to ? $vacation->date_to : $vacation->date_from }}</td>
                        </tr>



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
