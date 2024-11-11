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

        .change-place {
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
                        <button class="btn-all px-3" style="color: #FFFFFF; background-color: #274373;"
                            onclick="confirmAndRedirect()">
                            <img src="" alt=""> اعادة توزيع
                        </button>
                        <div class="colors d-flex mx-5">
                            <div class="only rounded p-1 px-2 mx-1">دورية به مفتش فقط</div>
                            <div class="urgent rounded p-1 px-2 mx-1">أمر خدمة</div>
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
            <!-- Container for the table layout -->
            <div class="container col-11">
                <div class="col-12 pt-5 pb-5" dir="rtl">
                    <!-- Main table with borders and responsive styling -->
                    <table id="custom-scroll-table" class="table table-bordered table-responsive table-container"
                        border="1" dir="rtl">
                        <?php $count = 1; ?> <!-- Counter for numbering rows -->
                        @foreach ($Groups as $Group)
                            <!-- Loop through each group -->
                            <thead>
                                <tr id="group-tr{{ $Group->id }}"> <!-- Header row for group -->
                                    <th scope="col" rowspan="2" style="background-color: #a5d0ffbd;">العدد</th>
                                    <!-- Column for numbering -->
                                    <th style="background-color: #97b8dd; border:none;"></th>
                                    <!-- Empty column for styling -->
                                    <th scope="col" rowspan="2" style="background-color: #97b8dd; border:none;">
                                        {{ $Group->name }}</th> <!-- Group name -->
                                    @foreach ($Group['days_name'] as $day)
                                        <!-- Loop through days of the week -->
                                        <th scope="col" class="{{ $day }}" style="background-color: #e4f1ffbd;">
                                            {{ $day }}</th> <!-- Day name column -->
                                    @endforeach
                                </tr>
                                <tr> <!-- Subheader row for days -->
                                    <th style="background-color: #97b8dd; border:none;"></th>
                                    <!-- Empty column for styling -->
                                    @foreach ($Group['days_num'] as $num)
                                        <!-- Loop through days numbers -->
                                        <th scope="col" class="{{ $num }}" style="background-color: #e4f1ffbd;">
                                            {{ $num }}</th> <!-- Day number column -->
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @if ($Group['teams'])
                                    <!-- Check if group has teams -->
                                    @foreach ($Group['teams'] as $team)
                                        <!-- Loop through each team -->
                                        <tr class="group-table" id="group-{{ $Group->id }}-team-tr{{ $team->id }}">
                                            <td colspan="3"
                                                style="text-align: center; color:black; background-color: {{ count($team['inspectors']) == 1 ? '#4edfd0ba' : '#11509bb3' }};">
                                                {{ $team->name }}</td>
                                            <!-- Team name and background color based on the number of inspectors -->
                                            @foreach ($Group['days_num'] as $index => $num)
                                                <!-- Loop through day numbers for styling -->
                                                <td class="group-table-num{{ $num }}"
                                                    style="background-color:{{ $team['colors'][$index] }}"></td>
                                                <!-- Team-specific colors for each day -->
                                            @endforeach
                                        </tr>
                                        @foreach ($team['inspectors'] as $index => $inspector)
                                            <!-- Loop through each inspector in the team -->
                                            <tr id="group-{{ $Group->id }}-team-inspector-tr{{ $inspector->id }}"
                                                class="one-team" data-team-id="{{ $team->id }}">
                                                <td style="background-color: #a5d0ffbd">{{ $count }}</td>
                                                <!-- Row number for inspectors -->
                                                <td style="background-color:#c5d8ed; color:#274373; font-weight: 600;"
                                                    colspan="2">
                                                    <!-- Inspector's grade and name -->
                                                    @if ($inspector->user && $inspector->user->grade)
                                                        {{ $inspector->user->grade->name }} /
                                                    @endif
                                                    {{ $inspector->name }}
                                                </td>
                                                @foreach ($inspector['missions'] as $index2 => $mission)
                                                    <!-- Loop through inspector's missions -->
                                                    @if ($mission)
                                                        <!-- Check if mission exists -->
                                                        @php
                                                            // Assign classes based on mission type (day off or vacation)
                                                            $class = '';
                                                            if ($mission->day_off) {
                                                                $class = 'dayoff';
                                                            } elseif ($mission->vacation_id) {
                                                                $class = 'rest';
                                                            }
                                                        @endphp
                                                        <td class="{{ $class }} drop-target"
                                                            id="day-{{ $index2 + 1 }}"
                                                            style="background-color: {{ $class != '' ? '' : $inspector['colors'][$index2] }}">
                                                            @if (!$mission->day_off && isset($inspector['vacations'][$index2]))
                                                                <!-- Check if vacation is set -->
                                                                <ul>
                                                                    <li style="color: white;font-weight: bold">
                                                                        {{ $inspector['vacations'][$index2] }}</li>
                                                                    <!-- Display vacation -->
                                                                </ul>
                                                            @else
                                                                <ul style="list-style:none;">
                                                                    <!-- Display mission details -->

                                                                    @if (isset($inspector['points'][$index2]) && count($inspector['points'][$index2]) > 0)
                                                                        @foreach ($inspector['points'][$index2] as $index3 => $point)
                                                                            <li class="change-place"
                                                                                id="point-{{ $point->id }}"
                                                                                draggable="true">{{ $point->name }}
                                                                                <hr />
                                                                            </li>
                                                                            <!-- Display points with draggable feature -->
                                                                        @endforeach
                                                                    @endif
                                                                    @if (
                                                                        // !$mission->day_off &&
                                                                        isset($inspector['instant_missions'][$index2]) && count($inspector['instant_missions'][$index2]) > 0)
                                                                        @foreach ($inspector['instant_missions'][$index2] as $instant_mission)
                                                                            <li class="urgent">
                                                                                {{ $instant_mission->label }}</li>
                                                                            <!-- Display urgent missions -->
                                                                        @endforeach
                                                                    @endif
                                                                    @if (
                                                                        !$mission->day_off &&
                                                                            isset($inspector['personal_missions'][$index2]) &&
                                                                            count($inspector['personal_missions'][$index2]) > 0)
                                                                        @foreach ($inspector['personal_missions'][$index2] as $personal_mission)
                                                                            <li class="task">
                                                                                {{ $personal_mission->point->name }}</li>
                                                                            <!-- Display personal tasks -->
                                                                        @endforeach
                                                                    @endif
                                                                </ul>
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td style="background-color: #d6d6d6"></td>
                                                        <!-- Empty cell for days without missions -->
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <?php $count++; ?> <!-- Increment inspector count -->
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    let originalDate = null; // Variable to store the original date
    let originalTeamId = null; // Variable to store the original team ID
    let originalInspectorId = null; // Variable to store the original inspector ID
    const today = new Date().toISOString().split('T')[0]; // Current date in YYYY-MM-DD format

    function extractGroupId(id) {
        const match = id.match(/^group-\d+/);
        return match ? match[0] : null;
    }

    function extractTeamId(trId) {
        const match = trId.match(/-team-tr(\d+)/);
        return match ? match[1] : null;
    }

    function getDayDate(dayNumber) {
        const now = new Date();
        const referenceDate = new Date(now.getFullYear(), now.getMonth(), 1);

        if (isNaN(dayNumber) || dayNumber < 1) {
            console.log('Invalid day number');
            return null;
        }

        const targetDate = new Date(referenceDate);
        targetDate.setDate(referenceDate.getDate() + (parseInt(dayNumber, 10) - 1));

        const year = targetDate.getFullYear();
        const month = String(targetDate.getMonth() + 1).padStart(2, '0');
        const day = String(targetDate.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function getDayNumber(element) {
        const id = element.getAttribute('id');
        if (id && id.startsWith('day-')) {
            const dayNumber = id.substring(4);
            return getDayDate(dayNumber);
        }
        return null;
    }

    function isDuplicateContent(targetElement, content) {
        const existingItems = targetElement.querySelectorAll('li');
        for (const item of existingItems) {
            if (item.innerHTML === content) {
                return true;
            }
        }
        return false;
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

                originalTeamId = closestTr.getAttribute('data-team-id'); // Get the original team ID
                originalInspectorId = closestTr.id.split('inspector-tr')[1]; // Get the original inspector ID
                originalDate = getDayNumber(originalParent.closest('td')); // Get the original date
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
            if (!targetDate) {
                Swal.fire({
                    title: 'خطأ',
                    text: '  لا يمكن النقل  ',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'تم !'
                });
                return;
            }

            // if (event.target.classList.contains('dayoff')) {
            //     Swal.fire({
            //         title: 'خطأ',
            //         text: 'لا يمكن نقل نقطة الى أيام العطلة',
            //         icon: 'error',
            //         confirmButtonColor: '#3085d6',
            //         confirmButtonText: 'تم !'
            //     });
            //     return;
            // }
            
            if (event.target.classList.contains('rest')) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'لا يمكن نقل نقطة الى يوم اجازة',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'تم !'
                });
                return;
            }

            if (targetDate < today) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'لا يمكن نقل نقطة الى ايام سابقة',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'تم !'
                });
                return;
            }
            // Validate the original date - only allow dragging from today or future dates
            if (originalDate < today) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'لا يمكن نقل نقطة من يوم سابق',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'تم !'
                });
                return;
            }
            const targetTr = event.target.closest('tr');
            const targetGroupId = extractGroupId(targetTr.id);
            const targetTeamId = targetTr.getAttribute('data-team-id'); // Get the new team ID
            const targetInspectorId = targetTr.id.split('inspector-tr')[1]; // Get the new inspector ID

            if (originalGroup !== targetGroupId) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'لا يمكن نقل النقطة إلى مجموعة مختلفة',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'تم !'
                });
                return;
            }

            const [group, group_id] = targetGroupId.split('-');
            const targetElement = event.target;
            const targetUl = targetElement.querySelector('ul');

            if (isDuplicateContent(targetElement, draggedElement.innerHTML)) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'لا يمكن نقل نقطة بنفس الاسم',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'تم !'
                });
                return;
            }

            var pointString = draggedElement ? draggedElement.getAttribute('id') : null;

            var [point, group_point_id] = pointString.split('-');

            const newLi = document.createElement('li');

            $.ajax({
                url: "{{ route('point.dragdrop') }}",
                type: 'get',
                data: {
                    group_id: group_id,
                    team_id: targetTeamId, // New team ID
                    group_point_id: group_point_id,
                    date: targetDate, // New date
                    old_team_id: originalTeamId, // Original team ID
                    old_date: originalDate, // Original date
                    old_inspector_id: originalInspectorId, // Original inspector ID
                    new_inspector_id: targetInspectorId // New inspector ID
                },
                success: function(response) {
                    if (response) {
                        Swal.fire({
                            title: 'تم بنجاح',
                            text: "تم نقل النقطة إلى الفريق الآخر بنجاح",
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'تم !',
                        });
                        newLi.innerHTML = draggedElement.innerHTML;
                        newLi.className = draggedElement.className;
                        newLi.id = draggedElement.id;
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
                    } else {
                        Swal.fire({
                            title: 'خطأ',
                            text: "لا يمكن نقل النقطة لهذا الفريق",
                            icon: 'error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'تم !',
                        });
                    }
                },
            });

        }
    });

    function confirmAndRedirect() {
        // Display confirmation dialog
        if (confirm("Do you want to complete this action?")) {
            // If the user clicks "Yes," redirect to the specific route
            window.location.href = "{{ route('refresh.inspector.mission') }}";
        }
    }
</script>
