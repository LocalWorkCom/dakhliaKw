@extends('layout.main')

@push('style')
    <style>
        .radio-buttons {
            display: none;
            margin-top: 10px;
        }
    </style>
@endpush
@section('title')
    تعديل
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('group.view') }}">المجموعات </a></li>
                <li class="breadcrumb-item"><a href="{{ route('groupTeam.index', $group_id) }}">الدوريات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل </a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p>تعديل الدورية او اضافه مفتش
                <p></p>

            </div>
        </div>
    </div>

    <br>
    <div class="row" dir="rtl">

        <div class="container moftsh col-11 mt-3 pt-3 pb-3 ">
            <h3 class="pt-3 px-md-5 px-3"> من فضلك قم باضافة المفتشون الى الدورية</h3>

            <form class="edit-grade-form" id="" action="{{ route('groupTeam.update', $id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group col-md-11 mx-4">
                    <label for="name" style="display: flex; flex-direction: column-reverse;">الاسم</label>
                    <input type="text" id="name" name="name"
                        class="form-control @error('name') is-invalid @enderror" placeholder="الاسم" dir="rtl"
                        value="{{ old('name', $team->name) }}">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-11 mx-4">
                    <label for="working_tree_id" class="d-flex justify-content-start pt-3 pb-2">اختر
                        نظام العمل</label>
                    <select class="form-control" name="working_tree_id" id="working_tree_id">
                        <option selected disabled>اختار من القائمة</option>
                        @foreach ($workTrees as $workTree)
                            <option value="{{ $workTree->id }}"
                                {{ $team->working_tree_id == $workTree->id ? 'selected' : '' }}>
                                {{ $workTree->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('working_tree_id'))
                        <span class="text-danger">{{ $errors->first('working_tree_id') }}</span>
                    @endif
                </div>

                @foreach ($inspectorGroups as $index => $inspectorGroup)
                    <div class="select-boxes mt-5 mx-4 col-10" dir="rtl">
                        <div class="check-one d-flex justify-content-start">
                            <input type="checkbox" class="toggle-radio-buttons mx-2" id="checkbox{{ $index }}"
                                name="inspectors_ids[]" value="{{ $inspectorGroup['inspector_id']->id }}"
                                {{ in_array($inspectorGroup['inspector_id']->id, old('inspectors_ids', $selectedInspectors)) ? 'checked' : '' }}>
                            <label for="checkbox{{ $index }}">{{ $inspectorGroup['inspector_id']->name }}</label>
                        </div>
                    </div>
                @endforeach

                <span class="text-danger span-error">
                    @if ($errors->has('nothing_updated'))
                        {{ $errors->first('nothing_updated') }}
                    @endif
                </span>

                <div class="container col-11">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">
                        <button type="submit" class="btn-blue" id="btn-submit">حفظ</button>
                    </div>
                </div>
            </form>
        @endsection

        @push('scripts')
            {{-- <script>
                // JavaScript to handle checkbox and radio button visibility
                document.querySelectorAll('.toggle-radio-buttons').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        // Find the associated radio-buttons div
                        const radioButtonsId = 'radio-buttons' + this.id.replace('checkbox', '');
                        const radioButtons = document.getElementById(radioButtonsId);
                        if (radioButtons) {
                            radioButtons.style.display = this.checked ? 'block' : 'none';
                        }
                    });
                });
            </script> --}}
        @endpush
