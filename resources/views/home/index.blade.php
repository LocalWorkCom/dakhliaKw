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
            <div class="row col-12 d-flex">
                {{-- @if (appear('stat')) --}}
                @foreach ($Statistics as $statistic)
                    <div class="col-md-3 col-sm-12 col-12 d-block" dir="rtl"
                        style="visibility: {{ !in_array($statistic->id, $UserStatistic->toArray()) ? 'hidden' : 'visible' }}">
                        <div class="graph-card" style="background-color: #ffffff;">
                            <div class="d-flex">
                                <i class="fa-solid fa-user-group" style="color: #8E52B1;"></i>
                                <h2 class="mx-3">{{ $statistic->name }}</h2>
                            </div>
                            <h1>
                                {{ $counts[$statistic->name] ?? 0 }} <!-- Display the count for each statistic -->
                            </h1>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>


    <div class="row desktop-view">
        <div class="container col-11 mt-3 p-0 d-flex" style="background-color: transparent;" dir="rtl">
            <!-- First Card -->
            <div class=" col-12  circle-graph-card " style="background-color: #ffffff;">
                <div class="">
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
                            <button id="searchBtn" class="btn btn-primary mx-2">
                                <i class="fa fa-search"></i> <!-- FontAwesome search icon -->
                            </button>
                        </div>
                    </div>

                    <div class="d-flex col-12 mt-3">
                        <div class="color" style="background-color:#AA1717"></div>
                        <h2 class="info col-2">عدد مخالفات</h2>
                        <h2 class="h2 mx-5">{{ $totalViolations }}</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#F8A723"></div>
                        <h2 class="info col-2">عدد زيارات</h2>
                        <h2 class="h2 mx-5">{{ $totalPoints }}</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#274373"></div>
                        <h2 class="info col-2">عدد المفتشين</h2>
                        <h2 class="h2 mx-5">{{ $totalInspectors }}</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#274373"></div>
                        <h2 class="info col-2">عدد المواقع</h2>
                        <h2 class="h2 mx-5">{{ $totalGroupPoints }}</h2>
                    </div>
                    <div class="d-flex col-12 col-sm-12">
                        <div class="color" style="background-color:#274373"></div>
                        <h2 class="info col-2">عدد اوامر الخدمة </h2>
                        <h2 class="h2 mx-5">{{ $totalIdsInstantMission }}</h2>
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

        const chartColors = ["#AA1717", "#F8A723", "#274373", "#274373", "#274373"];
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
                    url: "{{ route('statistic.search') }}", // Update with your search route
                    method: 'GET',
                    data: searchData, // Pass the search data
                    success: function(response) {
                        console.log("Response: ", response);

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
