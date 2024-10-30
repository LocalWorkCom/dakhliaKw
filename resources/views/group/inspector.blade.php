@extends('layout.main')

@push('style')
    <style>
        .radio-buttons {
            display: none;
            margin-top: 10px;
        }

        .inspector-item {
            margin-top: 10px;
        }
    </style>
@endpush

@section('title')
    اضافه
@endsection

@section('content')
<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('group.view') }}">المجموعات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة </a></li>
            </ol>
        </nav>
    </div>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p>اضافة المفتشون</p>
            </div>
        </div>
    </div>
    <br>
    <div class="row" dir="rtl">
        <div class="container moftsh col-11 mt-3 pt-3 pb-3 ">

            <h4 class="pt-3 px-md-4 px-2 d-flex justify-content-start" style="color: #274373;">
            <i class="fa-sharp-duotone fa-solid fa-check-double mx-2 "></i>
              اسم المجموعة :  <span style="font-weight:600;">    {{ $group->name }} </span>
            </h4>
            <h3 class="pt-3 px-md-5 px-3"> من فضلك قم باضافة المفتشون</h3>
            <div class="input-group mx-2">
                <div class="form-outline mt-4">
                    <input type="search" id="search" class="form-control mx-4" placeholder="بحث"
                        style="width: 100% !important; border-radius: 0px !important;" />
                </div>
                <button type="button" class="btn mt-4" data-mdb-ripple-init>
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <form class="edit-grade-form" id="add-form" action="{{ route('group.groupAddInspectors', $id) }}"
                method="POST">
                @csrf
                <div class="select-boxes mt-5 mx-4 col-10" dir="rtl">
                    @foreach ($inspectors as $inspector)
                        <div class="check-one d-flex justify-content-start inspector-item">
                            <input type="checkbox" class="toggle-radio-buttons mx-2" value="{{ $inspector->id }}"
                                id="inspector_{{ $inspector->id }}" name="inspectore[]">
                            <label for="inspector_{{ $inspector->id }}">{{ $inspector->name }}</label>
                        </div>
                    @endforeach

                    @isset($inspectorsIngroup)
                        @foreach ($inspectorsIngroup as $inspector)
                            <div class="check-one d-flex justify-content-start inspector-item">
                                <input type="checkbox" class="toggle-radio-buttons mx-2" value="{{ $inspector->id }}"
                                    id="inspectorin_{{ $inspector->id }}" checked name="inspectorein[]">
                                <label for="inspectorin_{{ $inspector->id }}">{{ $inspector->name }}</label>
                            </div>
                        @endforeach
                    @endisset
                </div>

                <span class="text-danger span-error" id="inspectore-error"></span>

                <div class="container col-11 ">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">
                        <button type="submit" class="btn-blue" id="btn-submit">حفظ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            document.querySelectorAll('.inspector-item').forEach(function(item) {
                const label = item.querySelector('label').textContent.toLowerCase();
                console.log(label);
                if (label.includes(searchValue)) {
                    // console.log(searchValue);
                    // item.style.display = 'block';
                    item.style.setProperty('display', 'flex', 'important');

                } else {
                    item.style.setProperty('display', 'none', 'important');

                }
            });
        });
    </script>
@endpush
