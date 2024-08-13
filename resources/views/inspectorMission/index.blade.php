@extends('layout.main')
@section('title')
    مــهام التــفتــيش
@endsection


@section('content')
    <section>
        <div class="row ">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> مــهام التــفتــيش </p>

                    <div class="d-flex ">
                        <button class="btn-all px-3 " style="color: #FFFFFF; background-color: #274373;"
                            onclick="window.print()">
                            <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
                        </button>
                        <div class="colors  d-flex mx-5">

                            <div class="night rounded p-1 px-3 mx-1"> ليل</div>
                            <div class="morning  rounded p-1 px-3 mx-1 ">صبح</div>
                            <div class="afternoon rounded p-1 px-3 mx-1">عصر</div>
                            <div class="dayoff rounded p-1 px-3 mx-1">راحه</div>
                            <div class="rest rounded p-1 px-3 mx-1">اجازات</div>
                            <div class=" guide rounded p-1 px-3 mx-1">: للارشاد </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <br>


        <div class="row col-12" id="days-table">
            <div class="container col-12">
                <div class="col-12" dir="rtl">
                    <table class="table table-bordered  table-responsive table-container" border="1" dir="rtl">
                        <?php $count = 1; ?>
                        @foreach ($Groups as $Group)
                            <thead>
                                <tr>
                                    <th scope="col" rowspan="2" style="background-color: #a5d0ffbd;">العدد</th>
                                    <th scope="col" rowspan="2" style="background-color: #e4f1ffbd;">
                                        {{ $Group->name }}</th>
                                    @foreach ($Group['days_name'] as $day)
                                        <th scope="col" class="{{ $day }}">{{ $day }}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($Group['days_num'] as $num)
                                        <th scope="col" class="{{ $num }}">{{ $num }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($Group['teams'] as $team)
                                    <tr class="group-table">
                                        <td colspan="2"
                                            style=" text-align: center;color: white;background-color: darkgreen;">
                                            {{ $team->name }}</td>
                                        @foreach ($Group['days_num'] as $index => $num)
                                            <td style="background-color:{{ $team['colors'][$index] }}"></td>
                                        @endforeach
                                    </tr>

                                    @foreach ($team['inspectors'] as $index => $inspector)
                                        <tr>
                                            <td style="background-color: #a5d0ffbd">{{ $count }}</td>
                                            <td style="color: white;background-color: darkcyan;">
                                                {{ $inspector->name }}</td>
                                            @foreach ($inspector['missions'] as $index2 => $mission)
                                                @if ($mission)
                                                    @php
                                                        $class = '';
                                                        if ($mission->day_off) {
                                                            $class = 'dayoff';
                                                        } elseif ($mission->vacation_id) {
                                                            $class = 'rest';
                                                        }
                                                    @endphp

                                                    <td class="{{ $class }}"
                                                        style="background-color: {{ $class != '' ? '' : $inspector['colors'][$index2] }}">
                                                        <ul>
                                                            @if (!$mission->day_off && isset($mission['points']) && count($mission['points']) > 0)
                                                                @foreach ($mission['points'] as $point)
                                                                    <li style="color: white;font-weight: bold">
                                                                        {{ $point->name }}</li>
                                                                @endforeach
                                                            @endif

                                                            @if (!$mission->day_off && isset($mission['instant_missions']) && count($mission['instant_missions']) > 0)
                                                                @foreach ($mission['instant_missions'] as $instant_mission)
                                                                    <li class="urgent">{{ $instant_mission->label }}</li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                    </td>
                                                @else
                                                    <td style="background-color: #756262"></td>
                                                @endif
                                            @endforeach
                                        </tr>
                                        <?php $count++; ?>
                                    @endforeach
                                @endforeach
                            </tbody>
                        @endforeach

                    </table>

                </div>
            </div>
        </div>
    </section>
@endsection
