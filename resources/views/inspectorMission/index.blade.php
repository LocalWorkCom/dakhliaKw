@extends('layout.main')
@section('title')
    مــهام التــفتــيش
@endsection

@section('style')
    <style>
        .dragging {
            opacity: 0.5;
        }

        .drop-target {
            border: 2px dashed #aaa;
            /* Visual cue for drop target */
        }
        .change-place{
           color: white;
           font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <section>
        <!-- Title Section -->
        <div class="row">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p>مــهام التــفتــيش</p>
                    <div class="d-flex">
                        <button class="btn-all px-3" style="color: #FFFFFF; background-color: #274373;" onclick="printDiv()">
                            <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
                        </button>
                        <div class="colors d-flex mx-5">
                            <div class="only rounded p-1 px-2 mx-1">فريق به مفتش فقط</div>
                            <div class="task rounded p-1 px-2 mx-1">مهمة</div>
                            <div class="urgent rounded p-1 px-2 mx-1">أمر فوري</div>
                            @foreach ($working_times as $time)
                                <div class="{{ $time->class_name }} rounded p-1 px-2 mx-1"
                                    style="background-color: {{ $time->color }}; color: white;">
                                    {{ $time->name }}
                                </div>
                            @endforeach
                            <div class="change rounded p-1 px-2 mx-1">منقول</div>
                            <div class="dayoff rounded p-1 px-2 mx-1">راحه</div>
                            <div class="rest rounded p-1 px-2 mx-1">اجازات</div>
                            <div class="guide rounded p-1 px-2 mx-1">: للارشاد</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <!-- Main Table Section -->
        <div class="row col-12" id="days-table">
            <div class="container col-11">
                <div class="col-12 pt-5 pb-5" dir="rtl">
                    <table class="table table-bordered table-responsive table-container" border="1" dir="rtl">
                        <?php $count = 1; ?>
                        @foreach ($Groups as $Group)
                            <thead>
                                <tr id="group-tr{{ $Group->id }}">
                                  
                                    <th scope="col" rowspan="2" style="background-color: #a5d0ffbd;" >العدد</th>
                                    <th style=" background-color: #97b8dd; border:none;"></th>
                                    <th scope="col" rowspan="2" style="background-color: #97b8dd; border:none;" >{{ $Group->name }}
                                    </th>
                                    @foreach ($Group['days_name'] as $day)
                                        <th scope="col" class="{{ $day }}" style="background-color: #e4f1ffbd;">
                                            {{ $day }}</th>
                                    @endforeach
                                </tr>
                                <tr> <th style=" background-color: #97b8dd; border:none;"></th>
                                    @foreach ($Group['days_num'] as $num)
                                   
                                        <th scope="col" class="{{ $num }}" style="background-color: #e4f1ffbd;">
                                            {{ $num }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @if ($Group['teams'])
                                    @foreach ($Group['teams'] as $team)
                                        <tr class="group-table" id="group-{{ $Group->id }}-team-tr{{ $team->id }}" >
                                          
                                            <td colspan="3"
                                                style="text-align: center; color:black; background-color: {{ count($team['inspectors']) == 1 ? '#4edfd0ba' : '#11509bb3' }};">
                                                {{ $team->name }}
                                            </td>
                                            @foreach ($Group['days_num'] as $index => $num)
                                                <td class="group-table-num{{ $num }}"
                                                    style="background-color:{{ $team['colors'][$index] }}" ></td>
                                            @endforeach
                                        </tr>
                                        @foreach ($team['inspectors'] as $index => $inspector)
                                            <tr id="group-{{ $Group->id }}-team-inspector-tr{{ $inspector->id }}">
                                               
                                                <td style="background-color: #a5d0ffbd">{{ $count }}</td>
                                                <td style="background-color:#c5d8ed; color:#274373; font-weight: 600;"colspan="2">
                                                    @if ($inspector->user && $inspector->user->grade)
                                                        {{ $inspector->user->grade->name }} /
                                                    @endif
                                                    {{ $inspector->name }}
                                                </td>

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
                                                        <td class="{{ $class }} drop-target"
                                                            id="day-{{ $index2+1 }}"
                                                            style="background-color: {{ $class != '' ? '' : $inspector['colors'][$index2] }}">
                                                            @if (!$mission->day_off && isset($inspector['vacations'][$index2]))
                                                                <ul>
                                                                    <li style="color: white;font-weight: bold">
                                                                        {{ $inspector['vacations'][$index2] }}</li>
                                                                </ul>
                                                            @else
                                                                <ul style="list-style:none;">
                                                                    @if (!$mission->day_off && isset($inspector['points'][$index2]) && count($inspector['points'][$index2]) > 0)
                                                                        @foreach ($inspector['points'][$index2] as $index3 => $point)
                                                                            <li class="change-place"
                                                                                id="point{{ $point->id }}"
                                                                                draggable="true">
                                                                                {{ $point->name }}
                                                                            </li>
                                                                        @endforeach
                                                                    @endif
                                                                    @if (
                                                                        !$mission->day_off &&
                                                                            isset($inspector['instant_missions'][$index2]) &&
                                                                            count($inspector['instant_missions'][$index2]) > 0)
                                                                        @foreach ($inspector['instant_missions'][$index2] as $instant_mission)
                                                                            <li class="urgent">
                                                                                {{ $instant_mission->label }}</li>
                                                                        @endforeach
                                                                    @endif
                                                                    @if (
                                                                        !$mission->day_off &&
                                                                            isset($inspector['personal_missions'][$index2]) &&
                                                                            count($inspector['personal_missions'][$index2]) > 0)
                                                                        @foreach ($inspector['personal_missions'][$index2] as $personal_mission)
                                                                            <li class="task">
                                                                                {{ $personal_mission->point->name }}</li>
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
        var divToPrint = document.getElementById('days-table');
        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write('<html><head><title>Print</title>');
        newWin.document.write('</head><body onload="window.print()">');
        newWin.document.write(divToPrint.innerHTML);
        newWin.document.write('</body></html>');
        newWin.document.close();
        setTimeout(function() {
            newWin.close();
        }, 10);
    }

    // Script for drag and drop functionality
    let draggedElement = null;
    let originalParent = null;
    let originalGroup = null;
    const today = new Date().toISOString().split('T')[0]; // Current date in YYYY-MM-DD format

    function extractGroupId(id) {
        const match = id.match(/^group-\d+/);
        return match ? match[0] : null;
    }

    function getDayDate(dayNumber) {
        const now = new Date();
        // Define the reference start date (e.g., 01-08-2024)
        const referenceDate = new Date(now.getFullYear(), now.getMonth(), 1);

        // Ensure dayNumber is a valid number
        if (isNaN(dayNumber) || dayNumber < 1) {
            console.log('Invalid day number');
            return null;
        }

        // Calculate the target date by adding the day number to the reference date
        const targetDate = new Date(referenceDate);
        targetDate.setDate(referenceDate.getDate() + (parseInt(dayNumber, 10) -
            1)); // Subtract 1 to start from referenceDate

        // Format the date as YYYY-MM-DD
        const year = targetDate.getFullYear();
        const month = String(targetDate.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
        const day = String(targetDate.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function getDayNumber(element) {
        const id = element.getAttribute('id');
        if (id && id.startsWith('day-')) {
            const dayNumber = id.substring(4); // Remove 'day-' prefix to get the day number
            return getDayDate(dayNumber);
        }
        return null;
    }


    document.addEventListener('dragstart', (event) => {
        if (event.target.tagName === 'LI') {
            const closestTr = event.target.closest('tr');
            if (closestTr) {
                originalGroup = extractGroupId(closestTr.id);
                draggedElement = event.target;
                originalParent = event.target.parentNode;
                event.target.classList.add('dragging');
                event.dataTransfer.setData('text/plain', draggedElement.id);
            }
        }
    });

    document.addEventListener('dragover', (event) => {
        event.preventDefault();
    });

    document.addEventListener('drop', (event) => {
        if (event.target.tagName === 'TD' && event.target.classList.contains('drop-target')) {
            event.preventDefault();

            const targetDate = getDayNumber(event.target);
            console.log(targetDate);

            if (!targetDate) {
                console.log('Invalid drop target');
                return;
            }



            if (targetDate < today) {
                console.log('Cannot drop on past days');
                return;
            }

            const targetTr = event.target.closest('tr');
            const targetGroupId = extractGroupId(targetTr.id);

            if (originalGroup !== targetGroupId) {
                return;
            }

            const targetElement = event.target;
            const targetUl = targetElement.querySelector('ul');

            const newLi = document.createElement('li');
            newLi.innerHTML = draggedElement.innerHTML;
            newLi.className = draggedElement.className;
            newLi.draggable = true;

            if (targetUl) {
                targetUl.appendChild(newLi);
            } else {
                const newUl = document.createElement('ul');
                newUl.appendChild(newLi);
                targetElement.appendChild(newUl);
            }

            originalParent.removeChild(draggedElement);
            draggedElement.classList.remove('dragging');
            draggedElement = null;
            originalParent = null;
            originalGroup = null;
        }
    });
</script>
