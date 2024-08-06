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
                <li class="breadcrumb-item"><a href="{{ route('groupTeam.index', $group_id) }}">الفرق </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل </a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p>تعديل الفرق
                <p></p>

            </div>
        </div>
    </div>

    <br>
    <div class="row" dir="rtl">

        <div class="container moftsh col-11 mt-3 pt-3 pb-3 ">
            <h3 class="pt-3 px-md-5 px-3"> من فضلك قم باضافة المفتشون الى لفريق</h3>
            {{-- <div class="input-group mx-2">
                <div class="form-outline mt-4">
                    <input type="search" id="" class="form-control mx-4" placeholder="بحث"
                        style="width: 100% !important; border-radius: 0px !important;" />
                </div>
                <button type="button" class="btn  mt-4" data-mdb-ripple-init>
                    <i class="fas fa-search"></i>
                </button>

            </div> --}}
            <form class="edit-grade-form" id="" action="{{ route('groupTeam.update', $id) }}" method="POST">
                @csrf


                <div class="form-group col-md-11 mx-4">
                    <label for="name" style="display: flex;    flex-direction: column-reverse;"> الاسم</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="الاسم"
                        value="{{ $team->name }}">
                </div>

                {{-- {{ dd($inspectorGroups) }} --}}
                @foreach ($inspectorGroups as $index => $inspectorGroup)
                    <div class="select-boxes mt-5 mx-4 col-10" dir="rtl">
                        <div class="check-one d-flex justify-content-start">
                            <input type="checkbox" class="toggle-radio-buttons mx-2" id="checkbox{{ $index }}"
                                name="inspectors_ids[]" value="{{ $inspectorGroup['inspector_id']->id }}"
                                {{ in_array($inspectorGroup['inspector_id']->id, $selectedInspectors) ? 'checked' : '' }}>
                            <label for="checkbox{{ $index }}">{{ $inspectorGroup['inspector_id']->name }}</label>
                            <!-- Replace with inspector name if available -->
                        </div>

                        {{-- <div class="radio-buttons" id="radio-buttons{{ $index }}" style="display: none;">
                            <div class="d-flex justify-content-start">
                                @foreach ($inspectorGroup['group_team_ids'] as $groupIndex => $groupTeamId)
                                    @foreach ($allteams as $item)
                                        <input type="radio" id="radio{{ $index }}-{{ $groupIndex }}"
                                            name="inspectors_ids[]" value="{{ $groupTeamId }}"
                                            {{ $item->id == $groupTeamId ? 'checked' : '' }}>
                                        <label class="mx-4" for="radio{{ $index }}-{{ $groupIndex }}">
                                            {{ $groupTeamId }}
                                            {{ $item->name }}
                                        </label>
                                    @endforeach
                                @endforeach
                            </div>
                        </div> --}}
                    </div>
                @endforeach


                <span class="text-danger span-error" id="inspectore-error"></span>


                <div class="container col-11 ">
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
