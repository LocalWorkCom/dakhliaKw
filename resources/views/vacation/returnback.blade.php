<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>header</title>
    <script type="application/javascript" src="{{asset('frontend/js/bootstrap.min.js')}}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap-->
    <link href="{{ asset('frontend/styles/bootstrap.min.css') }}" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="{{ asset('frontend/styles/index.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/styles/responsive.css') }}">

</head>

<body>
    <div class="container col-10 py-3 mt-5 mb-5" style="border: 0.1px solid rgba(161, 161, 161, 0.61);"
        id="return-back">

        <div class="container col-12  pt-5 mt-2 mb-2  pb-5" style="border: 0.1px solid rgba(110, 156, 241, 0.795); ">
            <div class="header mx-5 mt-5">
                <div class="row d-flex justify-content-between ">
                    <div class=" request-headeer ">
                        <img src="{{ asset('frontend/images/apply.png') }}" alt="img">
                    </div>
                    <div class=" logo-request my-5 ">
                        <img src="{{ asset('frontend/images/logo.svg') }}" alt="img">
                        <p class="my-2">المدير العام</p>
                    </div>
                    <div class=" request-headeer">
                        <img src="{{ asset('frontend/images/return.png') }}" alt="img">
                    </div>
                </div>
                <div class="row d-flex justify-content-center mb-5">
                    <div class="background-request mb-5">
                        <p> عودة من اجازة</p>
                    </div>
                </div>
                <div class="row qoute ">
                    <p> السيد / مدير عام شئون قوة الشرطة , المحترم </p>
                </div>
                <div class="row qoute">
                    <p>بعد التحية , </p>
                </div>

                <div class="row d-flex justify-content-center ">
                    <div class="text-request">
                        <p>
                            @if ($vacation->employee && $vacation->employee->grade)
                                {{ $vacation->employee->grade->name }}
                            @endif
                            / {{ $vacation->employee->name }}
                        </p>
                    </div>
                </div>
                <div class="row d-flex justify-content-center mb-5">
                    <div class="text-request mb-5">
                        <p>رقم الملف / {{ convertToArabicNumerals($vacation->employee->file_number) }}
                        </p>
                    </div>
                </div>

                <div class="row qoute " dir="rtl">
                    <p class=" "> نحيطكم علما بأن المذكؤر أعلاه
                    <p>قد باشر العمل لدينا بتاريخ <span>
                            {{ convertToArabicNumerals(\Carbon\Carbon::parse($vacation->end_date)->translatedFormat('l j F Y')) }}
                        </span></p>
                    <p>بعد حصوله على أجازة <span>{{ $vacation->vacation_type->name }}</span> مدتها
                        <?php
                        $startDate = \Carbon\Carbon::parse($vacation->start_date);
                        $endDate = \Carbon\Carbon::parse($vacation->endDate);
                        $daysLeft = $startDate->diffInDays($endDate, false);
                        
                        ?>
                        <span>{{ convertToArabicNumerals($daysLeft) }}</span> أيام
                    </p>
                    <!-- Continue with your HTML structure -->
                    </p>
                    <p>
                        و الممنوحة له بالنشرة رقم :
                        <span> ( 1111222 )</span>
                        بدأت بتاريخ
                        <span>{{ convertToArabicNumerals(\Carbon\Carbon::parse($vacation->start_date)->translatedFormat('l j F Y')) }}
                        </span>
                    </p>


                </div>
                <br>


                <div class="row qoute " dir="rtl">
                    <p class="my-5" style="color: green;">و تقبلوا تحياتنا ,</p>
                </div>

                <div class="row inputs-request-handprint d-flex justify-content-between " style="  color:#04209b;"
                    dir="rtl">
                    <div class="d-block mb-4">
                        <p class="mt-4 mb-1">
                            توقيع رئيس القسم
                        </p>
                        <div class="handprint">

                        </div>
                    </div>
                    <div class="d-block mb-4">
                        <p class="mt-4 mb-1">
                            توقيع مدير ادارة العمليات

                        </p>
                        <div class="handprint">

                        </div>
                    </div>
                </div>

                <div class="row qoute " dir="rtl">
                    <p class="my-5 "> تعقيب / مدير ادارة الخدمات المالية و الادارية : </p>
                </div>

                <div class="container col-12 mt-3" style="border: 2px solid rgba(0, 0, 0, 0.61);">
                    <div class="row qoute d-flex justify-content-center " dir="rtl">
                        <p class="pt-2 ">اجراءات ادارة الخدمات المالية</p>
                    </div>
                </div>

                <div class="row qoute " dir="rtl">
                    <p class="mt-5 mb-5"> تتم تحديد الاجازة للمذكؤر اعلاه و ارسلت للادارة العامة لشؤن قوة الشرطة و
                        ذلك بتارييخ .. / .. / ....
                    </p>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
