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
            <div class="row col-12 d-flex  ">


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
            <div class="col-md-7 col-sm-12 col-12 mx-4 circle-graph-card " style="background-color: #ffffff;">
                <div class="">
                    <div class="d-flex ">
                        <i class="fa-solid fa-user-group" style="color: #8E52B1;"></i>
                        <h2 class="mx-3 h2-charts mb-3" style="text-align: right;">تقرير شهر اغسطس</h2>
                    </div>
                    <!-- Second Row: Pie Chart and Info -->
                    <div class="row">
                        <div class="col-md-6  col-sm-12 col-12 d-flex">

                            <div class="d-block col-md-12 col-sm-12 col-12 mt-5">
                                <div class="d-flex mb-3 ">
                                    <div class="color"></div>
                                    <h2 class="info col-5 " id="info1"> عدد المخالفات</h2>
                                    <h2 class="h2 mx-5">890</h2>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="color "></div>
                                    <h2 class="info col-5" id="info1"> عدد النقاط</h2>
                                    <h2 class="h2 mx-5 ">890</h2>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="color"></div>
                                    <h2 class="info col-5" id="info1"> عدد المفتشين</h2>
                                    <h2 class="h2 mx-5">890</h2>
                                </div>
                            </div>
                            <canvas id="myPieChart" width="150" height="90" class="mt-2"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12 col-12 mx-5 canvas-card " style="background-color: #ffffff;">
                <div class="d-flex">
                    <i class="fa-solid fa-user-group" style="color: #8E52B1;"></i>
                    <h2 class="mx-3 h2-charts mb-4" style="text-align: right;">احصائيات المستخدمين والموظفين خلال ثلاث
                        اشهر</h2>
                </div>
                <canvas id="barChart" style="width:100%;max-width:600px;"></canvas>
                <div class="d-flex col-md-8 col-sm-12 col-12 mt-3">
                    <div class="color"></div>
                    <h2 class="info  " id="info1"> مستخدم</h2>
                    <h2 class="h2 mx-5">890</h2>
                </div>
                <div class="d-flex col-md-8 col-sm-12 col-12">
                    <div class="color"></div>
                    <h2 class="info " id="info1"> موظف</h2>
                    <h2 class=" h2 mx-5">890</h2>
                </div>
            </div>
        </div>
    </div>
    <div class=" row mobile-view ">
        <div class=" container col-11 d-flex justify-content-between" style="background-color: transparent;">
            <div class="  col-12  " style="background-color: #ffffff; border-radius: 20px;" dir="rtl">
                <div class="d-block  col-12 mt-3">
                    <div class="d-flex">
                        <i class="fa-solid fa-user-group" style="color: #8E52B1;"></i>
                        <h2 class="mx-3 h2-charts mb-4" style="text-align: right;">احصائيات المستخدمين والموظفين خلال ثلاث
                            اشهر</h2>
                    </div>
                    <div class="d-md-flex d-sm-block justify-content-between">
                        <div class="d-sm-flex d-md-block d-flex mb-3 ">
                            <div class="color"></div>
                            <h2 class="info  " id="info1"> عدد المخالفات</h2>
                            <h2 class="h2 mx-5">890</h2>
                        </div>
                        <div class="d-sm-flex d-md-block d-flex mb-3">
                            <div class="color "></div>
                            <h2 class="info " id="info1"> عدد النقاط</h2>
                            <h2 class="h2 mx-5 ">890</h2>
                        </div>
                        <div class="d-sm-flex d-md-block d-flex mb-3">
                            <div class="color"></div>
                            <h2 class="info " id="info1"> عدد المفتشين</h2>
                            <h2 class="h2 mx-5">890</h2>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=" container col-11 d-flex justify-content-between" style="background-color: transparent;">
            <div class="  col-12  mt-2" style="background-color: #ffffff; border-radius: 20px;" dir="rtl">
                <div class="d-block  col-12 mt-3 ">
                    <div class="d-flex ">
                        <i class="fa-solid fa-user-group" style="color: #8E52B1;"></i>
                        <h2 class="mx-3 h2-charts mb-5" style="text-align: right;">تقرير شهر اغسطس</h2>
                    </div>
                    <div class="d-md-flex d-sm-block justify-content-between">
                        <div class="d-sm-flex d-md-block d-flex ">
                            <div class="color"></div>
                            <h2 class="info  " id="info1"> مستخدم</h2>
                            <h2 class="h2 mx-5">890</h2>
                        </div>
                        <div class="d-sm-flex d-md-block d-flex">
                            <div class="color"></div>
                            <h2 class="info" id="info1"> موظف</h2>
                            <h2 class=" h2 mx-5">890</h2>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <script>
        const xValues = ["الشهر الأول", "الشهر الثاني", "الشهر الثالث"];
        const employeesData = [55, 49, 44];
        const managersData = [24, 15, 30];
        const chartColors = ["#F8A723", "#274373"];

        // Create the bar chart
        new Chart("barChart", {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                        label: "موظف",
                        backgroundColor: chartColors[0],
                        data: employeesData
                    },
                    {
                        label: "مستخدم",
                        backgroundColor: chartColors[1],
                        data: managersData
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
        const dataValues = [25, 35, 40];
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
        document.getElementById('info1').innerText = `عدد المخالفات: ${dataValues[0]}`;
        document.getElementById('info2').innerText = `عدد النقاط: ${dataValues[1]}`;
        document.getElementById('info3').innerText = `عدد المفتشين: ${dataValues[2]}`;

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
