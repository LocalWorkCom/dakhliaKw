@extends('layout.main')
@section('title')
   تعديل
@endsection
@section('content')


<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('inspectors.index') }}">المفتشون</a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل مفتش</a></li>
            </ol>
        </nav>
    </div>
</div>

    <div class="row ">
        <div class="container welcome col-11">
            <p> المفتــــــشون </p>
        </div>
    </div>
    <br>
    <form id="inspector-form" action="{{ route('inspectors.update', $inspector->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row" dir="rtl">

            <div class="container moftsh col-11 mt-3 p-0 pb-3 ">
                <h3 class="pt-3  px-md-4  px-3 "> تعديل مفتش</h3>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="form-row  mb-2 pb-4">
                    <label class="px-md-4 px-3 col-12 " for=""> الرقم المدني / رقم الهوية</label>
                    <div class="input-group px-md-4 px-3 pt-3">
                        <input name="Id_number" disabled value="{{ $inspector->user->Civil_number }}" disabled type="text"
                            id="search-input" placeholder="ابحث هنا ....." style="width: 100% !important;">
                        <input type="hidden" name="user_id" value="{{ $inspector->user_id }}">
                        {{-- <div class="select-wrapper">
                        <div class="select-box d-flex justify-content-between " disable id="select-box">
                            <p> {{ $inspector->name }}</p>
                            <i class="fa-solid fa-angle-down" style="color: #a3a1a1;"></i>
                        </div>
                        <div class="options" id="options">
                            <div class="search-box">
                                <input name="Id_number" type="text" id="search-input" dir="rtl" placeholder="ابحث هنا ....." style="width: 100% !important;">
                            </div>
                            @foreach ($users as $user)
                              <div class="option"
                                     data-id="{{ $user->id }}"
                                     data-name="{{ $user->name }}"
                                     data-phone="{{ $user->phone }}"
                                     data-grade_id="{{ $user->grade ? $user->grade->name : '' }}">
                                    {{ $user->name }}
                                </div>
                            @endforeach

                        </div>
                    </div> --}}
                    </div>



                </div>
            </div>
        </div>


        <!--********** الجزأده مش هيظهر غير بعد ما يعمل سيرش********** -->

        <div class="container moftsh col-11 mt-5 p-0 pb-3 " id="user-details-section">
            <h3 class="pt-3  px-md-4 px-3 "> عرض النتائج </h3>
            <div class="form-row  mx-md-4 mx-1 mb-2 pb-4">
                <table class="table table-bordered" dir="rtl">
                    <tbody>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;">الرتبه</th>
                            <td style="background: #f5f6fa;" id="user-grade_id">
                                {{ $inspector->user->grade != '' ? $inspector->user->grade->name : 'لا يوجد رتبه' }}
                            </td>
                        </tr>
                        <input type="hidden" name="id" value="{{ $inspector->id }}" id="hidden-position">

                        <input type="hidden" name="position" value="{{ $inspector->user->grade_id }}" id="hidden-position">
                        <tr>
                            <th scope="row">الاسم</th>
                            <td id="user-name">{{ $inspector->user->name != ' ' ? $inspector->user->name : 'لايوجد اسم' }}</td>
                        </tr>
                        <input type="hidden" name="name" value="{{ $inspector->user->name }}" id="hidden-name">
                        <tr>
                            <th scope="row">رقم الهاتف</th>
                            <td id="user-phone">
                                {{ $inspector->user->phone != '' ? $inspector->user->phone : 'لا يوجد هاتف' }}</td>
                        </tr>
                        <input type="hidden" name="phone" value="{{ $inspector->user->phone }}" id="hidden-phone">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="container moftsh col-11 mt-5 p-0 pb-2 mb-3">
            <h3 class="pt-3  px-md-4 px-3 "> اختر المفتش </h3>
            <div class="form-row mx-md-4 mx-2 mb-2 d-block justify-content-start" dir="rtl">
                @if (Auth::user()->rule->name == 'localworkadmin' || Auth::user()->rule->name == 'superadmin')
                    <div class="form-group d-flex">
                        <div class="radio-btn  d-flex">
                            <input type="radio" id="slok"   @if ($inspector->type == 'slok') checked @endif name="type" value="slok" required>
                            <label for="slok">مفتش سلوك أنضباطى</label>
                        </div>
                    </div>
                    <div class="form-group d-flex">
                        <div class="radio-btn  d-flex">
                            <input type="radio" id="internslok"   @if ($inspector->type == 'internslok') checked @endif name="type" value="internslok" required>
                            <label for="internslok">مفتش متدرب سلوك أنضباطى</label>
                        </div>
                    </div>
                    <div class="form-group d-flex">
                        <div class="radio-btn  d-flex">
                            <input type="radio" id="internbilding"   @if ($inspector->type == 'internbilding') checked @endif name="type" value="internbilding" required>
                            <label for="internbilding">مفتش متدرب مبانى</label>
                        </div>
                    </div>
                    <div class="form-group d-flex">
                        <div class="radio-btn  d-flex">
                            <input type="radio" id="Buildings"   @if ($inspector->type == 'Buildings') checked @endif name="type" value="Buildings" required>
                            <label for="Buildings">مفتش مباني </label>
                        </div>
                    </div>
                @elseif (strpos(Auth::user()->department->name, 'السلوك') !== false)
                <div class="form-group d-flex">
                    <div class="radio-btn  d-flex">
                        <input type="radio" id="slok" @if ($inspector->type == 'slok') checked @endif name="type" value="slok" required>
                        <label for="slok">مفتش سلوك أنضباطى</label>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="radio-btn  d-flex">
                        <input type="radio" id="internslok"   @if ($inspector->type == 'internslok') checked @endif name="type" value="internslok" required>
                        <label for="internslok">مفتش متدرب سلوك أنضباطى</label>
                    </div>
                </div>
                @elseif (strpos(Auth::user()->department->name, 'مبانى') !== false)
                <div class="form-group d-flex">
                    <div class="radio-btn  d-flex">
                        <input type="radio" id="Buildings"   @if ($inspector->type == 'Buildings') checked @endif name="type" value="Buildings" required>
                        <label for="Buildings">مفتش مباني </label>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="radio-btn  d-flex">
                        <input type="radio" id="internbilding"   @if ($inspector->type == 'internbilding') checked @endif name="type" value="internbilding" required>
                        <label for="internbilding">مفتش متدرب مبانى</label>
                    </div>
                </div>
                @endif





                <div class="container col-12 ">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">

                        <button type="submit" class="btn-blue">
                            <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img" height="20px"
                                width="20px"> تعديل
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: transparent; border: none;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        &times;</button>
                    </button>
                </div>
                <div class="modal-body mb-3 mt-3 d-flex justify-content-center">
                    <div class="body-img-modal d-block ">
                        <img src="{{ asset('frontend/images/ordered.svg') }}" alt="">
                        <p>تمت الاضافه بنجاح</p>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection
@push('script')
    <script>
        const selectBox = document.getElementById('select-box');
        const options = document.getElementById('options');
        const searchInput = document.getElementById('search-input');
        const optionElements = document.querySelectorAll('.option');
        const userPosition = document.getElementById('user-grade_id');
        const hiddenPosition = document.getElementById('hidden-position');

        const userName = document.getElementById('user-name');
        const hiddenName = document.getElementById('hidden-name');
        const userPhone = document.getElementById('user-phone');
        const hiddenPhone = document.getElementById('hidden-phone');
        const userDetailsSection = document.getElementById('user-details-section');

        selectBox.addEventListener('click', function() {
            options.style.display = 'block';
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.select-wrapper')) {
                options.style.display = 'none';
            }
        });

        optionElements.forEach(option => {
            option.addEventListener('click', function() {
                selectBox.textContent = this.textContent;
                selectBox.dataset.value = this.dataset.id;

                // Update user details
                userPosition.textContent = this.dataset.grade_id;
                hiddenPosition.value = this.dataset.grade_id;
                userName.textContent = this.dataset.name;
                hiddenName.value = this.dataset.name;
                userPhone.textContent = this.dataset.phone;
                hiddenPhone.value = this.dataset.phone;

                // Show the user details section
                userDetailsSection.style.display = 'block';

                options.style.display = 'none';
            });
        });

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            optionElements.forEach(option => {
                if (option.textContent.toLowerCase().includes(searchTerm)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });


        // Check if there is a flash message for showing the modal
        document.addEventListener('DOMContentLoaded', function() {
            if (session('showModal')) {
                const modal = new bootstrap.Modal(document.getElementById('myModal'));
                modal.show();
            }
        });
    </script>
@endpush
