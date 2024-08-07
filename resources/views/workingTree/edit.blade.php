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
                        <input type="text" id="name" name="name" class="form-control" required dir="rtl"
                            value="{{ $workingTree->name }}" placeholder="ادخل نظام العمل" />
                    </div>
                </div>
                <div class="form-row mx-2 mb-2">
                    <div class="input-group moftsh px-md-5 px-3 pt-3 col-6 workings">
                        <label class="pb-3" for="working_days_num">عدد ايام العمل</label>
                        <select name="working_days_num" id="working_days_num" required
                            style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر</option>
                            @for ($i = 1; $i < 30; $i++)
                                <option value="{{ $i }}" @if ($i == $workingTree->working_days_num) selected @endif>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="input-group moftsh px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="holiday_days_num">عدد ايام الاجازات</label>
                        <select name="holiday_days_num" id="holiday_days_num" required
                            style="border: 0.2px solid rgb(199, 196, 196);">
                            <option value="">اختر</option>
                            @for ($i = 1; $i <= 30; $i++)
                                <option value="{{ $i }}" @if ($i == $workingTree->holiday_days_num) selected @endif>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div id="selected-values-container">
                    @for ($i = 1; $i <= $workingTree->working_days_num; $i++)
                        <div class="form-row mx-2 mb-2">
                            <div class="input-group moftsh px-md-5 px-3 pt-3">
                                <label class="pb-3" for="holiday{{ $i }}"> اليوم {{ $i }}</label>
                                <select name="holiday{{ $i }}" id="holiday{{ $i }}" required
                                    style="border: 0.2px solid rgb(199, 196, 196);">
                                    <option value="">اختر الفترة</option>
                                    @foreach ($workingTimes as $item)
                                        @php
                                            // Check if the working time ID is present for the current day
                                            $isSelected =
                                                $workingTree->workingTreeTimes->firstWhere('day_num', $i)
                                                    ?->working_time_id === $item->id;
                                        @endphp
                                        <option value="{{ $item->id }}"  @if ($isSelected) selected @endif>
                                            {{ $item->name }}
                                    @endforeach
                                </select>
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
            <select name="holiday-template" id="holiday-template" style="border: 0.2px solid rgb(199, 196, 196);" required>
                <option value="">اختر الفترة</option>
                @foreach ($workingTimes as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @push('scripts')
        <script>
            var working_days_num = "<?php echo $workingTree->working_days_num; ?>";
            document.addEventListener('DOMContentLoaded', () => {
                const workingsSelect = document.getElementById('working_days_num');
                workingsSelect.addEventListener('change', () => {
                    const selectedValue = parseInt(workingsSelect.value, 10);
                    const selectedValuesContainer = document.getElementById('selected-values-container');
                    const templateDiv = document.querySelector('.holidays-template');
                    const numberOfChildren = selectedValuesContainer.children.length;
                    const children = Array.from(selectedValuesContainer.children);
                    if (children.length > selectedValue) {
                        for (let i = selectedValue; i < children.length; i++) {
                            selectedValuesContainer.removeChild(children[i]);
                        }
                    }
                    for (let i = numberOfChildren + 1; i <= selectedValue; i++) {
                        // Clone the template div
                        const newDiv = templateDiv.cloneNode(true);
                        newDiv.style.display = 'block';
                        newDiv.classList.remove('holidays-template');

                        // Update the label and select attributes
                        const label = newDiv.querySelector('label');
                        const select = newDiv.querySelector('select');

                        label.setAttribute('for', `holiday${i}`);
                        label.textContent = `اليوم ${i}`;

                        select.setAttribute('name', `holiday${i}`);
                        select.setAttribute('id', `holiday${i}`);

                        // Append the new div to the container
                        selectedValuesContainer.appendChild(newDiv);
                    }
                });
            });
        </script>
    @endpush
@endsection
