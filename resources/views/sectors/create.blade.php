@extends('layout.main')
@push('style')
@endpush
@section('title')
    القطاعات
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sectors.index') }}">القطاعات</a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة قطاع</a></li>
            </ol>
        </nav>
    </div>
    {{-- <div class="row ">
        <div class="container welcome col-11">
            <p> القطــــاعات </p>
        </div>
    </div> --}}
    {{-- {{ dd($governments) }} --}}
    <br>
    <form class="edit-grade-form" id="Qta3-form" action=" {{ route('sectors.store') }}" method="POST">
        @csrf
        <div class="row" dir="rtl">
            <div id="first-container" class="container moftsh col-11 mt-3 p-0 pb-3">
                <div class="form-row mx-2 mb-2">
                    <h3 class="pt-3 px-md-5 px-3">اضف قطاع</h3>
                    <div class="input-group moftsh px-md-5 px-3 pt-3">
                        <label class="pb-3" for="name">ادخل اسم القطاع</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="قطاع واحد" required/>
                        <span class="text-danger span-error" id="name-error"></span>

                    </div>
                </div>
                <div class="container col-11">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">
                        <button type="button" id="next-button" class="btn-blue">التالى</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" dir="rtl">
            <div id="second-container" class="container moftsh col-11 mt-3 p-0 pb-3 hidden">
                <h3 class="pt-3 px-md-5 px-3">اضف محافظات داخل قطاع</h3>
                <div class="form-row mx-2">
                    <div class="form-group moftsh px-md-5 px-3 pt-3">
                        <h4 style="color: #274373; font-size: 24px;">حدد المحافظات المراد اضافتها</h4>
                    </div>
                </div>
                <div class="form-row col-11 mb-2 mt-3 mx-md-2">
                    @foreach (getgovernments() as $government)
                        <div class="form-group col-3 d-flex mx-md-4">
                            <input type="checkbox" name="governmentIDS[]" value="{{ $government->id }}" id="governmentIDS">
                            <label for="governmentIDS">{{ $government->name }}</label>
                        </div>
                    @endforeach
                    

                </div>
                <span class="text-danger span-error" id="governmentIDS-error"></span>
                <div class="container col-11">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">
                        <button type="submit" class="btn-blue">
                            <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img" height="20px"
                                width="20px"> اضافة
                        </button>
                        <button type="button" id="back-button" class="btn-back mx-2">
                            <img src="{{ asset('frontend/images/previous.svg') }}" alt="img" height="20px"
                                width="20px"> السابق</button>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')

<script>
    document.getElementById('Qta3-form').addEventListener('submit', function(event) {
        var checkboxes = document.querySelectorAll('input[name="governmentIDS[]"]');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

        if (!checkedOne) {
            event.preventDefault(); // Prevent form submission
            document.getElementById('governmentIDS-error').textContent = 'من فضلك اختر محافظه واحده على الأقل .';
        } else {
            document.getElementById('governmentIDS-error').textContent = ''; // Clear any error messages
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var nameInput = document.getElementById('name');
        var nextButton = document.getElementById('next-button');
        var nameError = document.getElementById('name-error');

        nameInput.addEventListener('input', function() {
            if (nameInput.value.trim() !== '') {
                nextButton.disabled = false;
            } else {
                nextButton.disabled = true;
            }
        });

        nextButton.addEventListener('click', function() {
            if (nameInput.value.trim() === '') {
                nameError.textContent = 'يرجى إدخال اسم القطاع';
            } else {
                nameError.textContent = ''; // Clear any previous error message
                document.getElementById('first-container').classList.add('hidden');
                document.getElementById('second-container').classList.remove('hidden');
            }
        });

        document.getElementById('back-button').addEventListener('click', function() {
            document.getElementById('second-container').classList.add('hidden');
            document.getElementById('first-container').classList.remove('hidden');
        });
    });
</script>

@endpush
