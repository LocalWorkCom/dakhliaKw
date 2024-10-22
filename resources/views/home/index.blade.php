@extends('layout.main')
@section('title')
    الرئيسيه
@endsection
@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
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
            <div class="row col-12 d-flex" style="flex-direction: row-reverse;">
                {{-- @if (appear('stat')) --}}
                @foreach ($Statistics as $statistic)
                    <div class="col-md-3 col-sm-12 col-12 " dir="rtl"
                        style="display: {{ !in_array($statistic->id, $UserStatistic->toArray()) ? 'none' : 'block' }}">
                        <a href="{{ $routes[$statistic->name] }}">
                            <div class="graph-card" style="background-color: #ffffff;">
                                <div class="d-flex">
                                    <i class="fa-solid fa-user-group" style="color: #8E52B1;"></i>
                                    <h2 class="mx-3">{{ $statistic->name }}</h2>
                                </div>
                                <h1>
                                    {{ $counts[$statistic->name] ?? 0 }}
                                    <!-- Display the count for each statistic -->
                                </h1>
                            </div>
                        </a>
                    </div>
                @endforeach

            </div>
        </div>
    </div>


    <div class="row desktop-view">
        <div class="container col-11 mt-3 p-0" style="background-color: transparent;" dir="rtl">
            <div class="row">
                <div class="col-12" id="first-chart">
                    <div class="circle-graph-card" style="background-color: #ffffff;">
                        <div class="d-block align-items-center mb-5">
                            <!-- Header with Month Name -->
                            @php
                                use Carbon\Carbon;
                                setlocale(LC_TIME, 'ar_AE.utf8'); // Set locale to Arabic
                                $month_name = Carbon::create()->month(date('n'))->translatedFormat('F'); // Arabic month name
                            @endphp
                            <div class="d-flex graph">
                                <img src="{{ asset('frontend/images/report.svg') }}" alt="logo">
                                <h2 class="col-12 h2-charts mb-3" style="text-align: right;">
                                    تقرير شهر <span id="month_name">{{ $month_name }}</span>
                                </h2>
                            </div>

                            <!-- Filters and Print Button Section -->
                            <div class="d-flex align-items-center">
                                @php
                                    $currentMonth = date('n'); // Get current month
                                    $currentYear = date('Y'); // Get current year
                                @endphp

                                <select id="month" class="month mx-2" name="month">
                                    @foreach (getMonthNames() as $index => $month)
                                        <option value="{{ $index + 1 }}"
                                            {{ $index + 1 == $currentMonth ? 'selected' : '' }}>
                                            {{ $month }}
                                        </option>
                                    @endforeach
                                </select>

                                <select id="year" class="month mx-2" name="year">
                                    @foreach (getListOfYears() as $year)
                                        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Print Chart Button -->
                                {{-- <button onclick="PrintImage()" class="btn btn-secondary mx-2"
                                    style="background-color: #274373; font-size: 15px; height: 48px; border: none;">
                                    طباعة التقرير
                                </button> --}}
                            </div>
                        </div>
                        <!-- Second Row: Pie Chart and Info -->
                        <div class="row">
                            <div class="col-md-6 col-sm-12" id="data-info1">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="color" style="background-color: #aa1717;"></div>
                                    <h2 class="info" id="info1">عدد المخالفات</h2>
                                    <h2 class="h2">{{ $violations }}</h2>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="color" style="background-color: #f8a723;"></div>
                                    <h2 class="info" id="info2">عدد الزيارات</h2>
                                    <h2 class="h2">{{ $points }}</h2>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="color" style="background-color: #274373;"></div>
                                    <h2 class="info" id="info3">عدد المفتشين</h2>
                                    <h2 class="h2">{{ $inspectors }}</h2>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <canvas id="myPieChart" class="mt-2"></canvas>
                                <div id="NoData"></div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4">
                <div class="row">
                    <!-- Filter Section (Dropdown + Search Button Side by Side) -->
                    <div class="col-md-9 d-flex align-items-center justify-content-start">
                        <label for="filter_type" class="month_label">اختر نوع الفلتر:</label>
                        <select id="filter_type" class="month form-select me-2" style="width: 300px;">
                            <option value="">اختر نوع الفلتر</option>
                            <option value="group" selected>مجموعة</option>
                            <option value="team">دورية</option>
                            <option value="inspector">مفتش</option>
                            <option value="group_point">مجموعة نقاط</option>
                            <option value="point">نقطة</option>
                        </select>
                        <div class="d-flex align-items-center mx-2">
                            <label for="date_from" class="month_label">من</label>
                            <input type="date" name="date_from" id="date_from" class="month mx-2"
                                value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="d-flex align-items-center mx-2">
                            <label for="date_to" class="month_label">الى</label>
                            <input type="date" name="date_to" id="date_to" class="month mx-2"
                                value="{{ date('Y-m-t') }}">
                        </div>
                    </div>


                    <div class="col-md-3">
                        <button id="compareBtn" class="btn" style="background-color:  #274373;color:white">
                            مقارنة
                        </button>
                    </div>

                </div>


                <div class="row">
                    <!-- Second Chart Section -->
                    <div class="col-md-6">
                        <div class="circle-graph-card p-4" style="background-color: #ffffff">
                          

                            <div class="mb-3">
                                <select id="GroupID1" class="month form-select mb-2">
                                    <option value="">اختر المجموعة</option>
                                    @foreach ($GroupDatas as $Group)
                                        <option value="{{ $Group->id }}">{{ $Group->name }}</option>
                                    @endforeach
                                </select>

                                <select id="GroupTeam1" class="month form-select mb-2" style="display: none">
                                    <option value="" selected>اختر الفرقة</option>
                                    @foreach ($GroupTeamDatas as $GroupTeam)
                                        <option value="{{ $GroupTeam->id }}">{{ $GroupTeam->name }}</option>
                                    @endforeach
                                </select>

                                <select id="Inspector1" class="month form-select mb-2" style="display: none">
                                    <option value="" selected>اختر مفتش</option>
                                    @foreach ($inspectorDatas as $Inspector)
                                        <option value="{{ $Inspector->id }}">{{ $Inspector->name }}</option>
                                    @endforeach
                                </select>

                                <select id="Point1" class="month form-select mb-2" style="display: none">
                                    <option value="" selected>اختر النقطة</option>
                                    @foreach ($PointDatas as $Point)
                                        <option value="{{ $Point->id }}">{{ $Point->name }}</option>
                                    @endforeach
                                </select>

                                <select id="GroupPoint1" class="month form-select mb-2" style="display: none">
                                    <option value="" selected>اختر مجموعة نقاط</option>
                                    @foreach ($GroupPointDatas as $GroupPoint)
                                        <option value="{{ $GroupPoint->id }}">{{ $GroupPoint->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-section">
                                        <div class="d-flex justify-content-between mb-2" id="violation_dev2">
                                            <div class="color" style="background-color: #aa1717;"></div>
                                            <h2 class="info">عدد المخالفات</h2>
                                            <h2 id="violation_num2">0</h2>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2" id="points_dev2">
                                            <div class="color" style="background-color: #f8a723;"></div>
                                            <h2 class="info">عدد الزيارات</h2>
                                            <h2 id="points_num2">0</h2>
                                        </div>
                                        <div class="d-flex justify-content-between" id="inspector_dev2">
                                            <div class="color" style="background-color: #274373;"></div>
                                            <h2 class="info">عدد المفتشين</h2>
                                            <h2 id="inspector_num2">0</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <canvas id="myPieChart2" class="mt-3"></canvas>
                                    <div id="NoData2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Third Chart Section -->
                    <div class="col-md-6">
                        <div class="circle-graph-card p-4" style="background-color: #ffffff">
                          

                            <div class="mb-3">
                                <select id="GroupID2" class="month form-select mb-2">
                                    <option value="">اختر المجموعة</option>
                                    @foreach ($GroupDatas as $Group)
                                        <option value="{{ $Group->id }}">{{ $Group->name }}</option>
                                    @endforeach
                                </select>

                                <select id="GroupTeam2" class="month form-select mb-2" style="display: none">
                                    <option value="" selected>اختر الفرقة</option>
                                    @foreach ($GroupTeamDatas as $GroupTeam)
                                        <option value="{{ $GroupTeam->id }}">{{ $GroupTeam->name }}</option>
                                    @endforeach
                                </select>

                                <select id="Inspector2" class="month form-select mb-2" style="display: none">
                                    <option value="" selected>اختر مفتش</option>
                                    @foreach ($inspectorDatas as $Inspector)
                                        <option value="{{ $Inspector->id }}">{{ $Inspector->name }}</option>
                                    @endforeach
                                </select>

                                <select id="Point2" class="month form-select mb-2" style="display: none">
                                    <option value="" selected>اختر النقطة</option>
                                    @foreach ($PointDatas as $Point)
                                        <option value="{{ $Point->id }}">{{ $Point->name }}</option>
                                    @endforeach
                                </select>

                                <select id="GroupPoint2" class="month form-select mb-2" style="display: none">
                                    <option value="" selected>اختر مجموعة نقاط</option>
                                    @foreach ($GroupPointDatas as $GroupPoint)
                                        <option value="{{ $GroupPoint->id }}">{{ $GroupPoint->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-section">
                                        <div class="d-flex justify-content-between mb-2" id="violation_dev3">
                                            <div class="color" style="background-color: #aa1717;"></div>
                                            <h2 class="info">عدد المخالفات</h2>
                                            <h2 id="violation_num3">0</h2>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2" id="points_dev3">
                                            <div class="color" style="background-color: #f8a723;"></div>
                                            <h2 class="info">عدد الزيارات</h2>
                                            <h2 id="points_num3">0</h2>
                                        </div>
                                        <div class="d-flex justify-content-between" id="inspector_dev3">
                                            <div class="color" style="background-color: #274373;"></div>
                                            <h2 class="info">عدد المفتشين</h2>
                                            <h2 id="inspector_num3">0</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <canvas id="myPieChart3" class="mt-3"></canvas>
                                    <div id="NoData3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="row desktop-view">
        <div class="container col-11 mt-3 p-0" style="background-color: transparent;" dir="rtl">
            <div class="row mt-4">
                <div class="col-12 canvas-card" style="background-color: #FFFFFF;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex graph">
                            <img src="{{ asset('frontend/images/report.svg') }}" alt="logo">
                            <h2 class="mx-3 h2-charts mb-4" style="text-align: right;">
                                احصائيات الفرق والمجموعات والمفتشون
                            </h2>
                        </div>

                        <!-- Filters and Print Button -->
                        <div class="d-flex flex-wrap align-items-center">
                            <select id="Group" class="month mx-2" name="group_id">
                                <option value="" disabled selected>اختر المجموعة</option>
                                @foreach ($Groups as $Group)
                                    <option value="{{ $Group->id }}">{{ $Group->name }}</option>
                                @endforeach
                            </select>

                            <select id="GroupTeam" class="month mx-2" name="group_team_id">
                                <option value="" selected>اختر الفرقة</option>
                            </select>

                            <div class="d-flex align-items-center mx-2">
                                <label for="date_from" class="month_label">من</label>
                                <input type="date" name="date_from" id="date_from" class="month mx-2"
                                    value="{{ date('Y-m-01') }}">
                            </div>

                            <div class="d-flex align-items-center mx-2">
                                <label for="date_to" class="month_label">الى</label>
                                <input type="date" name="date_to" id="date_to" class="month mx-2"
                                    value="{{ date('Y-m-t') }}">
                            </div>

                            <!-- Search Icon Button -->
                            <button id="searchBtn" class="btn btn-primary mx-2"
                                style="background-color: #274373; font-size: 15px; height: 48px; border: none;">
                                <i class="fa fa-search"></i>
                            </button>

                            <!-- Print Chart Button -->
                            <button onclick="PrintImage2()" class="btn btn-secondary mx-2"
                                style="background-color: #274373; font-size: 15px; height: 48px; border: none;">
                                طباعة التقرير
                            </button>
                        </div>

                    </div>

                    <div id="data-info2">
                        <div class="d-flex col-12 mt-3">
                            <div class="color" style="background-color:#AA1717"></div>
                            <h2 class="info col-2">عدد مخالفات</h2>
                            <h2 class="h2 mx-5" id="violations">{{ $totalViolations }}</h2>
                        </div>
                        <div class="d-flex col-12">
                            <div class="color" style="background-color:#F8A723"></div>
                            <h2 class="info col-2">عدد زيارات</h2>
                            <h2 class="h2 mx-5" id="points">{{ $totalPoints }}</h2>
                        </div>
                        <div class="d-flex col-12">
                            <div class="color" style="background-color:#274373"></div>
                            <h2 class="info col-2">عدد المفتشين</h2>
                            <h2 class="h2 mx-5" id="inspectors">{{ $totalInspectors }}</h2>
                        </div>
                        <div class="d-flex col-12">
                            <div class="color" style="background-color:#3C9A34"></div>
                            <h2 class="info col-2">عدد المواقع</h2>
                            <h2 class="h2 mx-5" id="group_points">{{ $totalGroupPoints }}</h2>
                        </div>
                        <div class="d-flex col-12">
                            <div class="color" style="background-color:#43B8CE"></div>
                            <h2 class="info col-2">عدد اوامر الخدمة</h2>
                            <h2 class="h2 mx-5" id="instant_mission">{{ $totalIdsInstantMission }}</h2>
                        </div>
                    </div>

                    <canvas id="barChart" style="width:100%; height: 300px;" class="barChart"></canvas>
                    <div id="NoData2"></div>
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
                var selectedMonthText = $('#month option:selected').text(); // Get the month name
                $('#month_name').html(selectedMonthText);

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
        });
        // Function to update the Pie Chart (assuming you're using Chart.js)
        function updatePieChart(violations, points, inspectors) {
            var ctx = document.getElementById('myPieChart').getContext('2d');
            if (violations == 0 && points == 0 && inspectors == 0) {

                $('#NoData').html("No Data");
            } else {
                $('#NoData').html("");

            }
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

        function updatePieChart2(violations, points, inspectors, instant_mission, group_point, type) {
            var ctx = document.getElementById('myPieChart2').getContext('2d');

            // Show/Hide No Data message
            if (violations == 0 && points == 0 && inspectors == 0) {
                $('#NoData2').html("No Data").show();
            } else {
                $('#NoData2').html("").hide();
            }

            // Update stats display
            $('#inspector_num2').text(inspectors);
            $('#violation_num2').text(violations);
            $('#points_num2').text(points);

            var labels = [],
                data = [],
                backgroundColor = [];

            if (type == 'group' || type == 'team') {
                labels = ['مخالفات', 'زيارات', 'مفتشون'];
                data = [violations, points, inspectors];
                backgroundColor = ['#aa1717', '#f8a723', '#274373'];
            } else if (type == 'inspector') {
                labels = ['مخالفات', 'زيارات'];
                data = [violations, points];
                backgroundColor = ['#aa1717', '#f8a723'];
                $('#inspector_dev2').attr('style', 'display: none !important;');


            } else if (type == 'point') {
                labels = ['مخالفات'];
                data = [violations];
                backgroundColor = ['#aa1717'];
                $('#inspector_dev2').attr('style', 'display: none !important;');
                $('#violation_dev2').attr('style', 'display: none !important;');

            } else if (type == 'group_point') {
                labels = ['مخالفات', 'زيارات'];
                data = [violations, points];
                backgroundColor = ['#aa1717', '#f8a723'];

                $('#inspector_dev2').attr('style', 'display: none !important;');

            }

            // Render Pie Chart
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Statistics',
                        data: data,
                        backgroundColor: backgroundColor,
                    }]
                },
                options: {
                    responsive: true,
                }
            });
        }

        function updatePieChart3(violations, points, inspectors, instant_mission, group_point, type) {
            var ctx = document.getElementById('myPieChart3').getContext('2d');

            // Show/Hide No Data message
            if (violations == 0 && points == 0 && inspectors == 0) {
                $('#NoData3').html("No Data").show();
            } else {
                $('#NoData3').html("").hide();
            }

            // Update stats display
            $('#inspector_num3').text(inspectors);
            $('#violation_num3').text(violations);
            $('#points_num3').text(points);

            var labels = [],
                data = [],
                backgroundColor = [];

            if (type == 'group' || type == 'team') {
                labels = ['مخالفات', 'زيارات', 'مفتشون'];
                data = [violations, points, inspectors];
                backgroundColor = ['#aa1717', '#f8a723', '#274373'];
            } else if (type == 'inspector') {
                labels = ['مخالفات', 'زيارات'];
                data = [violations, points];
                backgroundColor = ['#aa1717', '#f8a723'];

                $('#violation_dev3').attr('style', 'display: none !important;');


            } else if (type == 'point') {
                labels = ['مخالفات'];
                data = [violations];
                backgroundColor = ['#aa1717'];

                $('#inspector_dev3').attr('style', 'display: none !important;');
                $('#violation_dev3').attr('style', 'display: none !important;');
            } else if (type == 'group_point') {
                labels = ['مخالفات', 'زيارات'];
                data = [violations, points];
                backgroundColor = ['#aa1717', '#f8a723'];

                $('#inspector_dev3').attr('style', 'display: none !important;');
            }

            // Render Pie Chart
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Statistics',
                        data: data,
                        backgroundColor: backgroundColor,
                    }]
                },
                options: {
                    responsive: true,
                }
            });
        }
    </script>

    <script>
        const groups = @json($Groups);

        // Use the map function to extract the names of the groups
        const xValues = groups.map(group => group.name);

        // Display the extracted group names in the console (for verification)
        const ViolationData = groups.map(group => group.violations);
        const PointsData = groups.map(group => group.points);
        const IspectorData = groups.map(group => group.inspectors);
        const GroupPointsData = groups.map(group => group.group_points);
        const InstantmissionData = groups.map(group => group.ids_instant_mission);

        const chartColors = ["#AA1717", "#F8A723", "#274373", "#3C9A34", "#43B8CE"];
        // Create the bar chart
        new Chart("barChart", {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                        label: "مخالفات",
                        backgroundColor: chartColors[0],
                        data: ViolationData,
                        barThickness: 20
                    },
                    {
                        label: "زيارات",
                        backgroundColor: chartColors[1],
                        data: PointsData,
                        barThickness: 20
                    },
                    {
                        label: "مفتشين",
                        backgroundColor: chartColors[2],
                        data: IspectorData,
                        barThickness: 20
                    },
                    {
                        label: "المواقع",
                        backgroundColor: chartColors[3],
                        data: GroupPointsData,
                        barThickness: 20
                    },
                    {
                        label: "اوامر خدمة",
                        backgroundColor: chartColors[4],
                        data: InstantmissionData,
                        barThickness: 20
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

        <?php
        if ($violations == 0 && $points == 0 && $inspectors == 0) {?>

        $('#NoData').html("No Data");
        <?php }
        ?>

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
                        text: 'تفاصيل'
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


        function PrintImage() {
            var canvas = document.getElementById("myPieChart");
            var infoContent = document.querySelector("#data-info1").outerHTML; // Select the info block content
            var monthName = document.getElementById("month_name").innerText; // Get the Arabic month name

            // Create a new window for printing
            var win = window.open('', '_blank');

            // Build the HTML content for printing
            win.document.write(`
                    <html>
                        <head>
                            <title>Print Chart</title>
                            <style>
                                body { font-family: Arial, sans-serif; direction: rtl; text-align: right; }
                                h2 { margin: 5px 0; }
                                .info { font-weight: bold; }
                                .color { width: 20px; height: 20px; display: inline-block; margin-left: 10px; }
                            </style>
                        </head>
                        <body>
                            <div class="d-flex col-md-12">
                            <h2>تقرير شهر <span>${monthName}</span></h2>
                            ${infoContent}  <!-- Inject the info section -->
                            <div class="col-md-6">
                                <img src="${canvas.toDataURL()}" alt="Pie Chart" id="chartImg"/>
                                </div>
                            </div>
                        </body>
                    </html>
                `);
            var img = win.document.getElementById("chartImg");
            img.onload = function() {
                win.print();
                win.close(); // Optional: Close the window after printing
            };

            // Ensure the print window content is loaded and ready
            win.document.close();
        }

        function PrintImage2() {
            var canvas = document.getElementById("barChart");
            var infoContent = document.querySelector("#data-info2").outerHTML; // Select the info block content

            // Create a new window for printing
            var win = window.open('', '_blank');

            // Build the HTML content for printing
            win.document.write(`
                    <html>
                        <head>
                            <title>Print Chart</title>
                            <style>
                                body { font-family: Arial, sans-serif; direction: rtl; text-align: right; }
                                h2 { margin: 5px 0; }
                                .info { font-weight: bold; }
                                .color { width: 20px; height: 20px; display: inline-block; margin-left: 10px; }
                            </style>
                        </head>
                        <body>
                            <div class="d-flex col-md-12">
                                <h2>تقرير شهر</h2>
                                ${infoContent}  <!-- Inject the info section -->
                                <div class="col-md-6">
                                    <img id="chartImg" src="${canvas.toDataURL()}" alt="Pie Chart"/>
                                </div>
                            </div>
                        </body>
                    </html>
            `);

            // Wait for the image to load before printing
            var img = win.document.getElementById("chartImg");
            img.onload = function() {
                win.print();
                win.close(); // Optional: Close the window after printing
            };

            // Ensure the print window content is loaded and ready
            win.document.close();
        }

        function updateChart2(violations, points, inspectors, instant_mission, group_points, groups, teams, inspectors) {
            // var ctx = document.getElementById('barChart').getContext('2d');
            var xValues;
            var groupId = $('#Group').val();
            var groupTeamId = $('#GroupTeam').val();
            console.log(groupTeamId);
            var PointsData = [];
            var ViolationData;
            var IspectorData;
            var GroupPointsData;
            var InstantmissionData;
            let chart = true; // Use a descriptive variable name

            if (groupId && !groupTeamId) {

                if (teams.length == 0) {
                    chart = false;
                }
                xValues = teams.map(team => team.name);
                ViolationData = teams.map(team => team.violations);
                PointsData = teams.map(team => team.points);
                IspectorData = teams.map(team => team.inspectors);
                GroupPointsData = teams.map(team => team.group_points);
                InstantmissionData = teams.map(team => team.ids_instant_mission);

            }
            if (groupTeamId) {

                if (inspectors.length == 0) {
                    chart = false;
                }
                console.log(inspectors);

                xValues = inspectors.map(inspector => inspector.name);
                ViolationData = inspectors.map(inspector => inspector.violations);
                PointsData = inspectors.map(inspector => inspector.points);
                IspectorData = inspectors.map(inspector => inspector.inspectors);
                GroupPointsData = inspectors.map(inspector => inspector.group_points);
                InstantmissionData = inspectors.map(inspector => inspector.ids_instant_mission);
            }
            if (!groupId && !groupTeamId) {

                if (groups.length == 0) {
                    chart = false;
                }
                xValues = groups.map(group => group.name); // Fallback option if both are undefined
                ViolationData = groups.map(group => group.violations);
                PointsData = groups.map(group => group.points);
                IspectorData = groups.map(group => group.inspectors);
                GroupPointsData = groups.map(group => group.group_points);
                InstantmissionData = groups.map(group => group.ids_instant_mission);
            }
            console.log(PointsData);


            // Display the extracted group names in the console (for verification)


            if (chart) {
                $('#NoData2').html("");
                $('#barChart').show();
                const chartColors = ["#AA1717", "#F8A723", "#274373", "#3C9A34", "#43B8CE"];
                // Create the bar chart
                new Chart("barChart", {
                    type: "bar",
                    data: {
                        labels: xValues,
                        datasets: [{
                                label: "مخالفات",
                                backgroundColor: chartColors[0],
                                data: ViolationData,
                                barThickness: 20
                            },
                            {
                                label: "زيارات",
                                backgroundColor: chartColors[1],
                                data: PointsData,
                                barThickness: 20
                            },
                            {
                                label: "مفتشين",
                                backgroundColor: chartColors[2],
                                data: IspectorData,
                                barThickness: 20
                            },
                            {
                                label: "المواقع",
                                backgroundColor: chartColors[3],
                                data: GroupPointsData,
                                barThickness: 20
                            },
                            {
                                label: "اوامر خدمة",
                                backgroundColor: chartColors[4],
                                data: InstantmissionData,
                                barThickness: 20
                            }
                        ]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true
                            },
                            title: {
                                display: true,
                                text: 'Chart Title'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }

                });
            } else {
                $('#NoData2').html("No Data Available");
                $('#barChart').hide();
            }
        }


        $(document).ready(function() {
            $('#compareBtn').click(function(e) {
                e.preventDefault();
                var filter_id1 = 0;
                var filter_id2 = 0;
                // Get the values of each input
                var group_id1 = $('#GroupID1').val();
                var team_id1 = $('#GroupTeam1').val();
                var inspector_id1 = $('#Inspector1').val();
                var point_id1 = $('#Point1').val();
                var group_point_id1 = $('#GroupPoint1').val();
                var group_id2 = $('#GroupID2').val();
                var team_id2 = $('#GroupTeam2').val();
                var inspector_id2 = $('#Inspector2').val();
                var point_id2 = $('#Point2').val();
                var group_point_id2 = $('#GroupPoint2').val();
                var dateFrom = $('#date_from').val();
                var dateTo = $('#date_to').val();
                var type = $('#filter_type').val();

                if (type == 'group') {
                    filter_id1 = group_id1;
                    filter_id2 = group_id2;

                } else if (type == 'team') {
                    filter_id1 = team_id1;
                    filter_id2 = team_id2;


                } else if (type == 'inspector') {
                    filter_id1 = inspector_id1;
                    filter_id2 = inspector_id2;


                } else if (type == 'point') {
                    filter_id1 = point_id1;
                    filter_id2 = point_id2;


                } else if (type == 'group_point') {
                    filter_id1 = group_point_id1;
                    filter_id2 = group_point_id2;

                }
                // Create the data object to send
                var searchData = {
                    filter_id1: filter_id1,
                    filter_id2: filter_id2,
                    type: type,
                    date_from: dateFrom,
                    date_to: dateTo
                };
                console.log(searchData);

                // Call AJAX
                $.ajax({
                    url: "{{ route('home.compare.graph') }}", // Update with your search route
                    method: 'GET',
                    data: searchData, // Pass the search data
                    success: function(response) {
                        console.log("Response: ", response);
                        $('#violations1').html(response.totalViolations1);
                        $('#points1').html(response.totalPoints1);
                        $('#inspectors1').html(response.totalInspectors1);
                        $('#instant_mission1').html(response.totalIdsInstantMission1);
                        $('#group_points1').html(response.totalGroupPoints1);

                        $('#violations2').html(response.totalViolations2);
                        $('#points2').html(response.totalPoints2);
                        $('#inspectors2').html(response.totalInspectors2);
                        $('#instant_mission2').html(response.totalIdsInstantMission2);
                        $('#group_points2').html(response.totalGroupPoints2);

                        updatePieChart2(response.totalViolations1, response.totalPoints1,
                            response
                            .totalInspectors1, response.totalIdsInstantMission1, response
                            .totalGroupPoints1, type);

                        updatePieChart3(response.totalViolations2, response.totalPoints2,
                            response
                            .totalInspectors2, response.totalIdsInstantMission2, response
                            .totalGroupPoints2, type)
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error occurred during the search.');
                    }
                });
            });
            $('#searchBtn').click(function(e) {
                e.preventDefault();

                // Get the values of each input
                var groupId = $('#Group').val();
                var groupTeamId = $('#GroupTeam').val();
                var dateFrom = $('#date_from').val();
                var dateTo = $('#date_to').val();

                // Create the data object to send
                var searchData = {
                    group_id: groupId,
                    group_team_id: groupTeamId,
                    date_from: dateFrom,
                    date_to: dateTo
                };

                // Call AJAX
                $.ajax({
                    url: "{{ route('home.statistic.search') }}", // Update with your search route
                    method: 'GET',
                    data: searchData, // Pass the search data
                    success: function(response) {
                        console.log("Response: ", response);
                        $('#violations').html(response.totalViolations);
                        $('#points').html(response.totalPoints);
                        $('#inspectors').html(response.totalInspectors);
                        $('#instant_mission').html(response.totalIdsInstantMission);
                        $('#group_points').html(response.totalGroupPoints);
                        updateChart2(response.totalViolations, response.totalPoints, response
                            .totalInspectors, response.totalIdsInstantMission, response
                            .totalGroupPoints, response.groups, response.teams, response
                            .inspectors)
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error occurred during the search.');
                    }
                });
            });
            $('#Group').change(function(e) {
                e.preventDefault();
                var group_id = $(this).val();

                $.ajax({
                    url: "{{ route('group.teams') }}",
                    data: {
                        group_id: group_id
                    },
                    method: 'GET',
                    success: function(response) {
                        // Assuming `response` is an array of group teams with `id` and `name` properties
                        var groupTeamSelect = $('#GroupTeam');

                        // Clear previous options, except for the default one
                        groupTeamSelect.empty().append(
                            '<option value="" selected>اختر الفرقة</option>');

                        // Iterate through the response and add options dynamically
                        $.each(response, function(index, team) {
                            groupTeamSelect.append('<option value="' + team.id + '">' +
                                team.name +
                                '</option>');
                        });
                    },
                    error: function() {
                        alert('Error retrieving data');
                    }
                });
            });
            $('#filter_type').change(function(e) {

                if ($(this).val() == 'group') {
                    $('#GroupID1').show();
                    $('#GroupID2').show();

                    $('#GroupTeam1').hide();
                    $('#GroupTeam2').hide();

                    $('#GroupPoint1').hide();
                    $('#GroupPoint2').hide();

                    $('#Inspector1').hide();
                    $('#Inspector2').hide();

                    $('#Point1').hide();
                    $('#Point2').hide();
                } else if ($(this).val() == 'team') {

                    $('#GroupTeam1').show();
                    $('#GroupTeam2').show();

                    $('#GroupID1').hide();
                    $('#GroupID2').hide();

                    $('#GroupPoint1').hide();
                    $('#GroupPoint2').hide();

                    $('#Inspector1').hide();
                    $('#Inspector2').hide();

                    $('#Point1').hide();
                    $('#Point2').hide();
                } else if ($(this).val() == 'inspector') {
                    $('#Inspector1').show();
                    $('#Inspector2').show();


                    $('#GroupTeam1').hide();
                    $('#GroupTeam2').hide();

                    $('#GroupID1').hide();
                    $('#GroupID2').hide();

                    $('#GroupPoint1').hide();
                    $('#GroupPoint2').hide();

                    $('#Point1').hide();
                    $('#Point2').hide();

                } else if ($(this).val() ==
                    'group_point') {

                    $('#GroupPoint1').show();
                    $('#GroupPoint2').show();

                    $('#Inspector1').hide();
                    $('#Inspector2').hide();


                    $('#GroupTeam1').hide();
                    $('#GroupTeam2').hide();

                    $('#GroupID1').hide();
                    $('#GroupID2').hide();


                    $('#Point1').hide();
                    $('#Point2').hide();

                } else if ($(this).val() == 'point') {
                    $('#Point1').show();
                    $('#Point2').show();

                    $('#GroupPoint1').hide();
                    $('#GroupPoint2').hide();

                    $('#Inspector1').hide();
                    $('#Inspector2').hide();


                    $('#GroupTeam1').hide();
                    $('#GroupTeam2').hide();

                    $('#GroupID1').hide();
                    $('#GroupID2').hide();


                }
            });
        });
    </script>
@endsection
