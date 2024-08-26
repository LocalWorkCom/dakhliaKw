@extends('layout.main')

@section('title')
    تعديل
@endsection

@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('working_trees.list') }}">نظام العمل</a></li>
                <li class="breadcrumb-item" aria-current="page"> <a href=""> تعديل </a></li>
            </ol>
        </nav>
    </div>
    @include('inc.flash')
    <div class="row">
        <div class="container welcome col-11">
            <p> نظام العمل </p>
        </div>
    </div>
    <br>
    <div class="row" dir="rtl">
        <div class="container moftsh col-11 mt-3 p-0 pb-3 ">
            <h3 class="pt-3  px-md-5 px-3 "> من فضلك ادخل البيانات </h3>
            <form action="{{ route('working_tree.update', $workingTree->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="form-row mx-2 mb-2">
                    <div class="input-group moftsh px-md-5 px-3 pt-3">
                        <label class="pb-3" for="name">اسم نظام العمل</label>
                        <input type="text" id="name" name="name" class="form-control" dir="rtl"
                            value="{{ $workingTree->name }}" placeholder="ادخل نظام العمل" />
                    </div>
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-row mx-2 mb-2">
                    <div class="input-group moftsh px-md-5 px-3 pt-3 col-12 workings">
                        <label class="pb-3" for="working_days_num">عدد ايام </label>
                        <select name="working_days_num" id="working_days_num"
                            style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر</option>
                            @for ($i = 1; $i < 30; $i++)
                                <option value="{{ $i }}" @if ($i == $workingTree->working_days_num+ $workingTree->holiday_days_num) selected @endif>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @if ($errors->has('working_days_num'))
                            <span class="text-danger">{{ $errors->first('working_days_num') }}</span>
                        @endif
                    </div>
                    <div class="input-group moftsh px-md-5 px-3 pt-3 col-6" hidden>
                        <label class="pb-3" for="holiday_days_num">عدد ايام الاجازات</label>
                        <select name="holiday_days_num" id="holiday_days_num"
                            style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر</option>
                            @for ($i = 1; $i <= 30; $i++)
                                <option value="{{ $i }}" @if ($i == $workingTree->holiday_days_num) selected @endif>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @if ($errors->has('holiday_days_num'))
                            <span class="text-danger">{{ $errors->first('holiday_days_num') }}</span>
                        @endif
                    </div>
                </div>

                <div id="selected-values-container">
                    @for ($i = 1; $i <= $workingTree->working_days_num + $workingTree->holiday_days_num; $i++)
                        @php
                            // Find the WorkingTreeTime entry for the current day
                            $workingTreeTime = $workingTree->workingTreeTimes->firstWhere('day_num', $i);
                            // Determine if the checkbox should be checked
                            $isHolidayChecked = $workingTreeTime ? $workingTreeTime->is_holiday : 0;
                        @endphp
                        <div class="form-row mx-2 mb-2">
                            <div class="input-group moftsh px-md-5 px-3 pt-3">
                                <div class="checkbox-container">
                                    <input type="checkbox" name="holiday_checkbox{{ $i }}"
                                        id="holiday_checkbox{{ $i }}" class="holiday-check"
                                        style="height: 20px;" @if ($isHolidayChecked) checked @endif>
                                        <label for="holiday_checkbox{{ $i }}">  اليوم   {{ $i }} / حدد اذا كان عطلة </label>
                                </div>
                                <!-- <label class="pb-3" for="period{{ $i }}"> اليوم
                                    {{ $i }}</label> -->
                                @if (!$isHolidayChecked)
                                    <select name="period{{ $i }}" id="period{{ $i }}"
                                        style="border: 0.2px solid rgb(199, 196, 196);">
                                        <option value="">اختر الفترة</option>
                                        @foreach ($workingTimes as $item)
                                            @php
                                                $isSelected =
                                                    $workingTreeTime && $workingTreeTime->working_time_id === $item->id;
                                            @endphp
                                            <option value="{{ $item->id }}"
                                                @if ($isSelected) selected @endif>
                                                {{ $item->name }}
                                        @endforeach
                                    </select>
                                @else
                                    <select name="period{{ $i }}" id="period{{ $i }}" 
                                        style="border: 0.2px solid rgb(199, 196, 196);display:none">
                                        <option value="">اختر الفترة</option>
                                        @foreach ($workingTimes as $item)
                                            @php
                                                $isSelected =
                                                    $workingTreeTime && $workingTreeTime->working_time_id === $item->id;
                                            @endphp
                                            <option value="{{ $item->id }}"
                                                @if ($isSelected) selected @endif>
                                                {{ $item->name }}
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    @endfor

                </div>

                <div class="container col-11">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">
                        <button type="submit" class="btn-blue"><img src="{{ asset('frontend/images/edit.svg') }}"
                                alt="img" height="20px" width="20px"> تعديل</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Template Div -->
    <div class="form-row mx-2 mb-2 holidays-template" style="display: none;">
        <div class="input-group moftsh px-md-5 px-3 pt-3">
            <label class="pb-3" for="holiday-template"></label>
            <select name="holiday-template" id="holiday-template" style="border: 0.2px solid rgb(199, 196, 196);">
                <option value="">اختر الفترة</option>
                @foreach ($workingTimes as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
            @if ($errors->has('holiday-template'))
                <span class="text-danger">{{ $errors->first('holiday-template') }}</span>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const workingsSelect = document.getElementById('working_days_num');
                const selectedValuesContainer = document.getElementById('selected-values-container');
                const templateDiv = document.querySelector('.holidays-template');

                workingsSelect.addEventListener('change', () => {
                    const selectedValue = parseInt(workingsSelect.value, 10);
                    selectedValuesContainer.innerHTML = '';

                    for (let i = 1; i <= selectedValue; i++) {
                        const newDiv = templateDiv.cloneNode(true);
                        newDiv.style.display = 'block';
                        newDiv.classList.remove('holidays-template');

                        const label = newDiv.querySelector('label');
                        const select = newDiv.querySelector('select');

                        label.setAttribute('for', `period${i}`);
                        label.textContent = `    اليوم ${i} / حدد اذا كان عطلة `;

                        select.setAttribute('name', `period${i}`);
                        select.setAttribute('id', `period${i}`);
                        select.removeAttribute('required');

                        const checkboxLabel = document.createElement('label');
                        checkboxLabel.setAttribute('for', `holiday_checkbox${i}`);
                        // checkboxLabel.textContent = ' عطلة';

                        const checkboxContainer = document.createElement('div');
                        checkboxContainer.classList.add('checkbox-container');

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.style.height = '25px !important';
                        checkbox.style.width = '25px !important';
                        checkbox.setAttribute('name', `holiday_checkbox${i}`);
                        checkbox.setAttribute('id', `holiday_checkbox${i}`);
                        checkbox.classList.add('holiday-check');
                        checkboxContainer.appendChild(checkbox);
                        checkboxContainer.appendChild(checkboxLabel);

                        label.parentNode.insertBefore(checkboxContainer, label);
                        selectedValuesContainer.appendChild(newDiv);
                    }
                });

                $(document).on('click', '.holiday-check', function() {
                    console.log('ii');

                    const checkboxId = $(this).attr('id');
                    const match = checkboxId.match(/holiday_checkbox(\d+)/);

                    if (match) {
                        const holidayNumber = match[1];
                        const periodSelect = $(`#period${holidayNumber}`);
                        console.log(periodSelect);


                        if ($(this).is(':checked')) {
                            periodSelect.hide();
                            periodSelect.val('');
                            periodSelect.removeAttr('required');
                        } else {
                            console.log('tt');

                            periodSelect.show();
                            // periodSelect.attr('required', 'required');
                        }
                    }
                });
                document.querySelector('form').addEventListener('submit', function(e) {

                    let valid = true;
                    const checkboxes = document.querySelectorAll('.holiday-check');

                    checkboxes.forEach(checkbox => {
                        const number = checkbox.id.match(/\d+/)[0];
                        const periodSelect = document.getElementById(`period${number}`);
                        console.log(periodSelect);
                        if (!checkbox.checked && periodSelect.value === '') {
                            valid = false;
                            periodSelect.classList.add('is-invalid');
                        } else if (checkbox.checked && periodSelect.value != '') {
                            valid = false;
                            periodSelect.classList.add('is-invalid');

                        } else {
                            periodSelect.classList.remove('is-invalid');
                        }
                    });
                    if (!valid) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'تأكيد ',
                            text: "يرجى ملء فترات العمل بشكل صحيح.",
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'نعم، !',
                        }).then((result) => {
                            // if (result.isConfirmed) {
                            //     window.location.href = $(ele).attr('href');
                            // }
                        });
                        // alert('يرجى ملء جميع الحقول المطلوبة بشكل صحيح.');
                    }
                });
            });
        </script>
    @endpush
@endsection
