@extends('layout.main')
@section('title')
    الرئيسيه
@endsection
@section('content')
    <div class="row ">
        <div class="container welcome col-11">
            <p> {{ auth()->user()->name }} مرحـــــــــــــــبا بك </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 " style=" background-color: transparent;">
            <div class="row col-12 d-flex">
                <div class="col-md-3 col-sm-12 col-12  d-block" dir="rtl">
                    <div class=" graph-card" style="background-color: #ffffff;">


                        <div class="d-flex">
                            <i class="fa-solid fa-user-group" style="color: #8E52B1;"></i>
                            <h2 class="mx-3">الادارات</h2>
                        </div>
                        <h1>{{ $depCount }}</h1>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 col-12  d-block" dir="rtl">
                    <div class=" graph-card" style="background-color: #ffffff;">


                        <div class="d-flex">
                            <i class="fa-solid fa-user-group" style="color: #28A39C"></i>
                            <h2 class="mx-3">المجموعات</h2>
                        </div>
                        <h1>{{ $groups }}</h1>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 col-12  d-block" dir="rtl">
                    <div class=" graph-card" style="background-color: #ffffff;">


                        <div class="d-flex">
                            <i class="fa-solid fa-user-group" style="color: #8E52B1;"></i>
                            <h2 class="mx-3">اجمالى المستخدمين</h2>
                        </div>
                        <h1>{{ $userCount }}</h1>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12 col-12  d-block" dir="rtl">
                    <div class=" graph-card" style="background-color: #ffffff;">


                        <div class="d-flex">
                            <i class="fa-solid fa-user-group" style="color:   #F7AF15;"></i>
                            <h2 class="mx-3">اجمالى الموظفين</h2>
                        </div>
                        <h1>{{ $empCount }}</h1>
                    </div>
                </div>
            </div>
            <div class="row col-12 d-flex  ">

                <div class="col-md-3 col-sm-12 col-12  d-block" dir="rtl">
                    <div class=" graph-card" style="background-color: #ffffff;">


                        <div class="d-flex">
                            <i class="fa-solid fa-user-group" style="color: #8E52B1;"></i>
                            <h2 class="mx-3">الوارد</h2>
                        </div>
                        <h1>{{ $ioCount }}</h1>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12 col-12  d-block" dir="rtl">
                    <div class=" graph-card" style="background-color: #ffffff;">


                        <div class="d-flex">
                            <i class="fa-solid fa-user-group" style="color: #F7AF15;"></i>
                            <h2 class="mx-3">الصادر</h2>
                        </div>
                        <h1>{{ $outCount }}</h1>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 col-12  d-block" dir="rtl">
                    <div class=" graph-card" style="background-color: #ffffff;">


                        <div class="d-flex">
                            <i class="fa-solid fa-user-group" style="color: #259240;"></i>
                            <h2 class="mx-3">اوامر الخدمة</h2>
                        </div>
                        <h1>{{ $instantmissions }}</h1>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 col-12  d-block" dir="rtl">
                    <div class=" graph-card" style="background-color: #ffffff;">


                        <div class="d-flex">
                            <i class="fa-solid fa-user-group" style="color: #259240;"></i>
                            <h2 class="mx-3">الاجازات</h2>
                        </div>
                        <h1>{{ $employeeVacation }}</h1>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="row desktop-view">
        <div class="container col-11 mt-3 p-0 d-flex" style="background-color: transparent;" dir="rtl">
            <!-- First Card -->
            <div class=" col-12  circle-graph-card " style="background-color: #ffffff;">
                <div class="">
                    <div class=" d-flex justify-content-between">
                        <div class="d-flex graph">
                            <img src="{{ asset('frontend/images/report.svg') }}" alt="logo">
                            <h2 class="col-12 h2-charts mb-3" style="text-align: right;">تقرير شهر <span
                                    id="month_name"></span></h2>
                        </div>
                        @php
                            $currentMonth = date('n'); // Get current month as an integer (1 to 12)
                            $currentYear = date('Y'); // Get current year
                        @endphp
                        <div class="d-flex">

                            <select id="month" class="month mx-2" name="month">
                                @foreach (getMonthNames() as $index => $month)
                                    <option value="{{ $index + 1 }}"
                                        {{ $index + 1 == $currentMonth ? 'selected' : '' }}>
                                        {{ $month }}</option>
                                @endforeach
                            </select>

                            <select id="year" class="month" name="year">
                                @foreach (getListOfYears() as $year)
                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <!-- Second Row: Pie Chart and Info -->
                    <div class="row">
                        <div class="col-md-5  col-sm-12 col-12 d-flex">

                            <div class="d-block col-md-12 col-sm-12 col-12 mt-5">
                                <div class="d-flex mb-3">
                                    <div class="color" style="background-color: #aa1717;"></div>
                                    <h2 class="info col-5 " id="info1"> عدد المخالفات</h2>
                                    <h2 class="h2 mx-5">{{ $violations }}</h2>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="color " style="background-color: #f8a723;"></div>
                                    <h2 class="info col-5" id="info2"> عدد الزيارات </h2>
                                    <h2 class="h2 mx-5 ">{{ $points }}</h2>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="color" style="background-color: #274373;"></div>
                                    <h2 class="info col-5" id="info3"> عدد المفتشين</h2>
                                    <h2 class="h2 mx-5">{{ $inspectors }}</h2>
                                </div>
                            </div>
                            <canvas id="myPieChart" width="150" height="90" class="mt-2"></canvas>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="row desktop-view">
        <div class="container col-11 mt-3 p-0" style="background-color: transparent;" dir="rtl">
            <!-- First Row -->
            <!-- Second Row -->
            <div class="row mt-4">
                <div class="col-12 canvas-card" style="background-color: #FFFFFF;">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex graph">
                            <img src="{{ asset('frontend/images/report.svg') }}" alt="logo">
                            <h2 class="mx-3 h2-charts mb-4" style="text-align: right;"> احصائيات الفرق والمجموعات
                                والمفتشون
                            </h2>
                        </div>
                        <div class="d-flex">
                            <select id="month" class="month" name="month">
                                <option value="" disabled selected> اختر المجموعة</option>
                                <option value=""> المجموعة</option>
                                <option value=""> المجموعة</option>
                                <option value=""> المجموعة</option>
                            </select>
                            <select id="month" class="month mx-2" name="month">
                                <option value="" disabled selected> اختر الفرقة</option>
                                <option value=""> الفرقة</option>
                                <option value=""> الفرقة</option>
                                <option value=""> الفرقة</option>
                            </select>
                            <div class="d-flex">

                                <label for="date_from" class="month_label">من</label>
                                <input type="date" name="" id="date_from" class="month mx-2">
                            </div>
                            <div class="d-flex">

                                <label for="date_to" class="month_label">الي</label>

                                <input type="date" name="" id="date_to" class="month mx-2">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex col-12 mt-3">
                        <div class="color" style="background-color:#AA1717"></div>
                        <h2 class="info col-2">عدد مخالفات</h2>
                        <h2 class="h2 mx-5">20</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#F8A723"></div>
                        <h2 class="info col-2">عدد زيارات</h2>
                        <h2 class="h2 mx-5">10</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#274373"></div>
                        <h2 class="info col-2">اوامر الخدمة </h2>
                        <h2 class="h2 mx-5">200</h2>
                    </div>
                    <canvas id="barChart" style="width:100%;height: 300px;" class="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        $(document).ready(function() {
            // When either the month or year is changed, trigger the filter
            $('#month, #year').on('change', function() {
                var month = $('#month').val();
                var year = $('#year').val();
                $('#month_name').html(month);

                // Send AJAX request to the filter route
                $.ajax({
                    url: '{{ route('home.filter') }}',
                    method: 'GET',
                    data: {
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        // Update the UI with the new filtered data
                        $('h2:contains("عدد المخالفات") + .h2').text(response.violations);
                        $('h2:contains("عدد الزيارات") + .h2').text(response.points);
                        $('h2:contains("عدد المفتشين") + .h2').text(response.inspectors);

                        // If you have any charts, update them here as well
                        updatePieChart(response.violations, response.points, response
                            .inspectors);
                    }
                });
            });

            // Function to update the Pie Chart (assuming you're using Chart.js)
            function updatePieChart(violations, points, inspectors) {
                var ctx = document.getElementById('myPieChart').getContext('2d');
                var pieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['مخالفات', 'زيارات', 'مفتشون'],
                        datasets: [{
                            label: 'Statistics',
                            data: [violations, points, inspectors],
                            backgroundColor: ['#aa1717', '#f8a723', '#274373'],
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });
            }
        });
    </script>

    <script>
        const xValues = ["الشهر الأول", "الشهر الثاني", "الشهر الثالث"];
        const employeesData = [55, 49, 44];
        const managersData = [24, 15, 30];
        const IspectorData = [24, 15, 30];
        const chartColors = ["#AA1717", "#F8A723", "#274373"];
        // Create the bar chart
        new Chart("barChart", {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                        label: "مخالفات",
                        backgroundColor: chartColors[0],
                        data: employeesData,
                        barThickness: 60
                    },
                    {
                        label: "زيارات",
                        backgroundColor: chartColors[1],
                        data: managersData,
                        barThickness: 60
                    },
                    {
                        label: "مفتشين",
                        backgroundColor: chartColors[2],
                        data: IspectorData,
                        barThickness: 60
                    }
                ]
            },
            options: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script>
        const dataValues = [{{ $violations }}, {{ $points }}, {{ $inspectors }}];
        const labels = ["عدد المخالفات", "عدد النقاط", "عدد المفتشين"];
        const pieChartColors = ["#AA1717", "#F8A723", "#274373"];

        // Create the pie chart
        const ctx = document.getElementById('myPieChart').getContext('2d');
        const myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    backgroundColor: pieChartColors,
                    data: dataValues
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'تفاصيل  '
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return `${tooltipItem.label}: ${tooltipItem.raw}`;
                            }
                        }
                    }
                }
            }
        });

        // Update h2 elements with data
        // document.getElementById('info1').innerText = `عدد المخالفات: ${dataValues[0]}`;
        // document.getElementById('info2').innerText = `عدد النقاط: ${dataValues[1]}`;
        // document.getElementById('info3').innerText = `عدد المفتشين: ${dataValues[2]}`;

        // Function to draw text inside the pie chart
        function drawText() {
            const chartArea = myPieChart.chartArea;
            ctx.font = '20px Almarai';
            ctx.fillStyle = '#F4F7FD'; // Text color

            // Draw text for each segment
            const angles = myPieChart.data.datasets[0].data.map((value) => value / myPieChart.data.datasets[0].data.reduce((
                a, b) => a + b) * 2 * Math.PI);
            let startAngle = -Math.PI / 2;

            angles.forEach((angle, index) => {
                const endAngle = startAngle + angle * 2 * Math.PI;
                const middleAngle = (startAngle + endAngle) / 2;

                const x = (chartArea.left + chartArea.right) / 2 + Math.cos(middleAngle) * 30;
                const y = (chartArea.top + chartArea.bottom) / 2 + Math.sin(middleAngle) * 100;

                ctx.fillText(`${dataValues[index]}`, x, y); // Draw the value inside the segment

                startAngle = endAngle; // Move to the next segment
            });
        }

        // Call drawText function after chart is rendered
        myPieChart.canvas.addEventListener('mouseover', drawText);
        myPieChart.canvas.addEventListener('mouseout', drawText);
        myPieChart.update();
    </script>
@endsection
