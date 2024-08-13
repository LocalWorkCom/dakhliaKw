@extends('layout.main')

@push('style')
    <style>
        .radio-buttons {
            display: none;
            margin-top: 10px;
        }
    </style>
@endpush

@section('title', 'نقل')

@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('group.view') }}">المجموعات </a></li>
                <li class="breadcrumb-item"><a href="{{ route('groupTeam.index', $group_id) }}">الفرق </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> نقل </a></li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p>نقل فرق</p>
            </div>
        </div>
    </div>

    <br>
    <div class="row" dir="rtl">
        <div class="container moftsh col-11 mt-3 pt-3 pb-3">
            <h3 class="pt-3 px-md-5 px-3">من فضلك قم بنقل المفتشون من فريق إلى آخر</h3>
            <div class="input-group mx-2">
                <div class="form-outline mt-4">
                    <input type="search" id="search" class="form-control mx-4" placeholder="بحث"
                        style="width: 100% !important; border-radius: 0px !important;" />
                </div>
                <button type="button" class="btn mt-4" data-mdb-ripple-init>
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <form class="edit-grade-form" action="{{ route('groupTeam.transfer.update', $group_id) }}" method="POST">
                @csrf
                <div id="inspector-list">
                    @foreach ($inspectorGroups as $index => $inspectorGroup)
                        <div class="select-boxes mt-5 mx-4 col-10 inspector-item" dir="rtl">
                            <div class="check-one d-flex justify-content-start">
                                <input type="checkbox" class="toggle-radio-buttons mx-2" id="checkbox{{ $index }}"
                                    name="inspectors_ids[]" value="{{ $inspectorGroup['inspector_id']->id }}"
                                    {{ in_array($inspectorGroup['inspector_id']->id, $selectedInspectors) ? 'checked' : '' }}>
                                <input type="hidden" value="{{ $inspectorGroup['inspector_id']->user->Civil_number }}"
                                    id="Civil_number{{ $index }}" name="Civil_number">
                                <label for="checkbox{{ $index }}">
                                    {{ $inspectorGroup['inspector_id']->name }}
                                </label>
                            </div>
                            <div class="radio-buttons" id="radio-buttons{{ $index }}">
                                <div class="d-flex justify-content-start">
                                    @foreach ($allteams as $item)
                                        <input type="radio" id="radio{{ $index }}-{{ $item->id }}"
                                            name="team_id[{{ $inspectorGroup['inspector_id']->id }}]"
                                            value="{{ $item->id }}"
                                            {{ in_array($item->id, $inspectorGroup['group_team_ids']) ? 'checked' : '' }}>
                                        <label class="mx-4" for="radio{{ $index }}-{{ $item->id }}">
                                            {{ $item->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <span class="text-danger span-error" id="inspectore-error"></span>
                <div class="container col-11">
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
        document.querySelectorAll('.toggle-radio-buttons').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const radioButtonsId = 'radio-buttons' + this.id.replace('checkbox', '');
                const radioButtons = document.getElementById(radioButtonsId);
                if (radioButtons) {
                    radioButtons.style.display = this.checked ? 'block' : 'none';
                }
            });
            checkbox.dispatchEvent(new Event('change'));
        });

        document.getElementById('search').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            document.querySelectorAll('.inspector-item').forEach(function(item) {
                const label = item.querySelector('label').textContent.toLowerCase();
                const civilNumber = item.querySelector('input[type="hidden"]').value.toLowerCase();
                if (label.includes(searchValue) || civilNumber.includes(searchValue)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
@endpush
