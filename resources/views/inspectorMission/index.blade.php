@extends('layout.main')
@section('title')
    مــهام التــفتــيش
@endsection

@section('content')
    <section>
        <div class="row ">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <!-- Title Section -->
                    <p> مــهام التــفتــيش </p>

                    <!-- Print Button and Legend -->
                    <div class="d-flex ">
                        <button class="btn-all px-3 " style="color: #FFFFFF; background-color: #274373;" onclick="printDiv()">
                            <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
                        </button>

                        <div class="colors  d-flex mx-5">
                            <div class="night rounded p-1 px-2 mx-1"> ليل</div>
                            <div class="task rounded p-1 px-2 mx-1"> مهمة</div>
                            <div class="urgent rounded p-1 px-2 mx-1"> أمر فوري</div>
                            <div class="morning  rounded p-1 px-2 mx-1 ">صبح</div>
                            <div class="afternoon rounded p-1 px-2 mx-1">عصر</div>
                            <div class="change rounded p-1 px-2 mx-1">منقول</div>
                            <div class="dayoff rounded p-1 px-2 mx-1">راحه</div>
                            <div class="rest rounded p-1 px-2 mx-1">اجازات</div>
                            <div class=" guide rounded p-1 px-2 mx-1">: للارشاد </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <!-- Main Table Section -->
        <div class="row col-12" id="days-table">
            <div class="container col-12">
                <div class="col-12 pt-5 pb-5" dir="rtl">
                    <table class="table table-bordered  table-responsive table-container " border="1" dir="rtl">
                        <?php $count = 1; ?>

                        @foreach ($Groups as $Group)
                            <thead>
                                <tr>
                                    <!-- Table Headers for Group -->
                                    <th scope="col" rowspan="2" style="background-color: #a5d0ffbd;">العدد</th>
                                    <th scope="col" rowspan="2" style="background-color: #97b8dd;">
                                        {{ $Group->name }}</th>
                                    @foreach ($Group['days_name'] as $day)
                                        <th scope="col" class="{{ $day }}" style="background-color: #e4f1ffbd;">
                                            {{ $day }}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <!-- Day Numbers for the Group -->
                                    @foreach ($Group['days_num'] as $num)
                                        <th scope="col" class="{{ $num }}" style="background-color: #e4f1ffbd;">
                                            {{ $num }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @if ($Group['teams'])
                                    <!-- Teams and Their Missions -->
                                    @foreach ($Group['teams'] as $team)
                                        <tr class="group-table">
                                            <!-- Team Name Row -->
                                            <td colspan="2"
                                                style=" text-align: center;background-color: #e4f1ffbd; color:black;">
                                                {{ $team->name }}</td>
                                            @foreach ($Group['days_num'] as $index => $num)
                                                <!-- Colors for Each Day in the Team -->
                                                <td style="background-color:{{ $team['colors'][$index] }}"></td>
                                            @endforeach
                                        </tr>

                                        <!-- Inspectors and Their Missions -->
                                        @foreach ($team['inspectors'] as $index => $inspector)
                                            <tr>
                                                <!-- Inspector Number -->
                                                <td style="background-color: #a5d0ffbd">{{ $count }}</td>

                                                <td style="background-color:#c5d8ed; color:#274373; font-weight: 600;">

                                                    {{ $inspector->name }}</td>
                                                @foreach ($inspector['missions'] as $index2 => $mission)
                                                    @if ($mission)
                                                        <!-- Determine Class Based on Mission Type -->
                                                        @php
                                                            $class = '';
                                                            if ($mission->day_off) {
                                                                $class = 'dayoff';
                                                            } elseif ($mission->vacation_id) {
                                                                $class = 'rest';
                                                            }
                                                        @endphp

                                                        <!-- Mission Details -->
                                                        <td class="{{ $class }}"
                                                            style="background-color: {{ $class != '' ? '' : $inspector['colors'][$index2] }}">
                                                            @if (!$mission->day_off && isset($inspector['vacations'][$index2]))
                                                                <ul>
                                                                    <li style="color: white;font-weight: bold">
                                                                        {{ $inspector['vacations'][$index2] }}
                                                                    </li>
                                                                </ul>
                                                            @else
                                                                <ul>
                                                                    <!-- Mission Points -->
                                                                  
                                                                    @if (!$mission->day_off && isset($inspector['points'][$index2]) && count($inspector['points'][$index2]) > 0)
                                                                        @foreach ($inspector['points'][$index2] as $point)
                                                                            <li style="color: white;font-weight: bold">
                                                                                {{ $point->name }}</li>
                                                                        @endforeach
                                                                    @endif

                                                                    <!-- Instant Missions -->
                                                                    @if (
                                                                        !$mission->day_off &&
                                                                            isset($inspector['instant_missions'][$index2]) &&
                                                                            count($inspector['instant_missions'][$index2]) > 0)
                                                                        @foreach ($inspector['instant_missions'][$index2] as $instant_mission)
                                                                            <li class="urgent">
                                                                                {{ $instant_mission->label }}
                                                                            </li>
                                                                        @endforeach
                                                                    @endif
                                                                </ul>
                                                            @endif

                                                        </td>
                                                    @else
                                                        <td style="background-color: #d6d6d6"></td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <?php $count++; ?>
                                        @endforeach
                                    @endforeach
                                @endif
                            </tbody>
                        @endforeach

                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
<script>
    function printDiv() {
        // Select the section you want to print
        var divToPrint = document.getElementById('days-table'); // Change to the ID of the table section

        // Create a new window for printing
        var newWin = window.open('', 'Print-Window');

        newWin.document.open();

        // Write the HTML content to the new window and include the table's content
        newWin.document.write('<html><head><title>Print</title>');
        // newWin.document.write(
        //     '<link rel="stylesheet" type="text/css" href="{{ asset('frontend/css/your-css-file.css') }}">'
        //     ); // Include any required CSS
        newWin.document.write('</head><body onload="window.print()">');
        newWin.document.write(divToPrint.innerHTML);
        newWin.document.write('</body></html>');

        newWin.document.close();

        // Close the print window after printing
        setTimeout(function() {
            newWin.close();
        }, 10);
    }
</script>
