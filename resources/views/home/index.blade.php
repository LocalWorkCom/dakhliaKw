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
                                    {{ $counts[$statistic->name] ?? 0 }} <!-- Display the count for each statistic -->
                                </h1>
                            </div>
                        </a>
                    </div>
                @endforeach

            </div>
        </div>
    </div>


    <div class="row desktop-view" id="first-chart">
        <div class="container col-11 mt-3 p-0 d-flex" style="background-color: transparent;" dir="rtl">
            <!-- First Card -->

            <div class=" col-12  circle-graph-card " style="background-color: #ffffff;">
                <div class="">
                    <div id="printArea">
                        <button onclick="PrintImage()">Print Chart</button>
                    </div>

                    <div class=" d-flex justify-content-between">
                        @php

                            use Carbon\Carbon;
                            setlocale(LC_TIME, 'ar_AE.utf8'); // Set locale to Arabic
                            $month_name = Carbon::create()->month(date('n'))->translatedFormat('F'); // Outputs the full month name in Arabic, e.g., "سبتمبر"

                        @endphp
                        <div class="d-flex graph">
                            <img src="{{ asset('frontend/images/report.svg') }}" alt="logo">
                            <h2 class="col-12 h2-charts mb-3" style="text-align: right;">تقرير شهر <span
                                    id="month_name">{{ $month_name }}</span></h2>
                        </div>
                        @php
                            $currentMonth = date('n'); // Get current month as an integer (1 to 12)
                            $currentYear = date('Y'); // Get current year
                        @endphp
                        <div class="d-flex">

                            <select id="month" class="month mx-2" name="month">
                                @foreach (getMonthNames() as $index => $month)
                                    <option value="{{ $index + 1 }}" {{ $index + 1 == $currentMonth ? 'selected' : '' }}>
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
                            <div id="NoData">

                            </div>
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
                            <h2 class="mx-3 h2-charts mb-4" style="text-align: right;"> احصائيات الفرق والمجموعات والمفتشون
                            </h2>
                        </div>
                        <div class="d-flex">
                            <select id="Group" class="month" name="group_id">
                                <option value="" disabled selected> اختر المجموعة</option>
                                @foreach ($Groups as $Group)
                                    <option value="{{ $Group->id }}"> {{ $Group->name }}</option>
                                @endforeach
                            </select>

                            <select id="GroupTeam" class="month mx-2" name="group_team_id">
                                <option value="" selected> اختر الفرقة</option>
                            </select>

                            <div class="d-flex">
                                <label for="date_from" class="month_label">من</label>
                                <input type="date" name="date_from" id="date_from" class="month mx-2"
                                    value="{{ date('Y-m-01') }}">
                            </div>

                            <div class="d-flex">
                                <label for="date_to" class="month_label">الي</label>
                                <input type="date" name="date_to" id="date_to" class="month mx-2"
                                    value="{{ date('Y-m-t') }}">
                            </div>

                            <!-- Search Icon Button -->
                            <button id="searchBtn" class="btn btn-primary mx-2"
                                style="background-color: #274373;
                                            font-size: 15px;
                                            height: 48px;
                                            border: none;">
                                <i class="fa fa-search"></i> <!-- FontAwesome search icon -->
                            </button>
                        </div>
                    </div>

                    <div class="d-flex col-12 mt-3">
                        <div class="color" style="background-color:#AA1717"></div>
                        <h2 class="info col-2">عدد مخالفات</h2>
                        <h2 class="h2 mx-5" id="violations">{{ $totalViolations }}</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#F8A723"></div>
                        <h2 class="info col-2">عدد زيارات</h2>
                        <h2 class="h2 mx-5" id="points">{{ $totalPoints }}</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#274373"></div>
                        <h2 class="info col-2">عدد المفتشين</h2>
                        <h2 class="h2 mx-5" id="inspectors">{{ $totalInspectors }}</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#3C9A34"></div>
                        <h2 class="info col-2">عدد المواقع</h2>
                        <h2 class="h2 mx-5" id="group_points">{{ $totalGroupPoints }}</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#43B8CE"></div>
                        <h2 class="info col-2">عدد اوامر الخدمة </h2>
                        <h2 class="h2 mx-5" id="instant_mission">{{ $totalIdsInstantMission }}</h2>
                    </div>
                    <canvas id="barChart" style="width:100%;height: 300px;" class="barChart"></canvas>
                    <div id="NoData2">

                    </div>
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
        });
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
            var infoContent = document.querySelector(".d-block").outerHTML; // Select the info block content
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
                    <img src="${canvas.toDataURL()}" alt="Pie Chart"/>
                    </div>
                </div>
            </body>
        </html>
    `);

            // Print and reload the print window
            win.document.close();
            win.print();
            // win.location.reload();
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
                console.log("g7657467");

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


        // Call drawText function after chart is rendered
        // myPieChart.canvas.addEventListener('mouseover', drawText);
        // myPieChart.canvas.addEventListener('mouseout', drawText);
        // myPieChart.update();

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
                    groupTeamSelect.empty().append('<option value="" selected>اختر الفرقة</option>');

                    // Iterate through the response and add options dynamically
                    $.each(response, function(index, team) {
                        groupTeamSelect.append('<option value="' + team.id + '">' + team.name +
                            '</option>');
                    });
                },
                error: function() {
                    alert('Error retrieving data');
                }
            });
        });
        $(document).ready(function() {
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
        });
    </script>
@endsection
