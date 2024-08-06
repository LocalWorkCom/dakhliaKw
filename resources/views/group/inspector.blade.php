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
            <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة مجموعة </a></li>
        </ol>
    </nav>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <div class="d-flex justify-content-between">
            <p>اضافة المفتشون
            <p></p>

        </div>
    </div>
</div>
<br>
<div class="row" dir="rtl">
    <div class="container moftsh col-11 mt-3 pt-3 pb-3 ">
        <h3 class="pt-3 px-md-5 px-3"> من فضلك قم باضافة المفتشون</h3>
        <div class="input-group mx-2">
            <div class="form-outline mt-4">
                <input type="search" id="" class="form-control mx-4" placeholder="بحث"
                    style="width: 100% !important; border-radius: 0px !important;" />
            </div>
            <button type="button" class="btn  mt-4" data-mdb-ripple-init>
                <i class="fas fa-search"></i>
            </button>

        </div>
    <form class="edit-grade-form" id="add-form" action="{{ route('group.groupAddInspectors',$id) }}"  method="POST">
        @csrf
        <div class="select-boxes mt-5 mx-4 col-10" dir="rtl">
            {{-- @isset($inspectors )&&  @isset( $inspectorsIngroup)
                لا يوجد مفتشين متاحين
            @endisset --}}
            @foreach ($inspectors as $inspector)
            <div class="check-one d-flex justify-content-start">
                <input type="checkbox" class="toggle-radio-buttons mx-2" value="{{ $inspector->id }}" id="inspector_{{ $inspector->id }}" name="inspectore[]">
                <label for="checkbox1">{{ $inspector->name }}</label>
            </div>  
            @endforeach
            @isset($inspectorsIngroup)
            @foreach ($inspectorsIngroup as $inspector)
            <div class="check-one d-flex justify-content-start">
                <input type="checkbox" class="toggle-radio-buttons mx-2" value="{{ $inspector->id }}" id="inspectorin_{{ $inspector->id }}" checked name="inspectorein[]">
                <label for="checkbox1">{{ $inspector->name }}</label>
            </div>  
            @endforeach
            @endisset
           
            
        </div>

        <span class="text-danger span-error" id="inspectore-error"></span>

        
        <div class="container col-11 ">
            <div class="form-row d-flex justify-content-end mt-4 mb-3">

                <button type="submit" class="btn-blue"  id="btn-submit">حفظ</button>
            </div>
        </div>


    </form>
       
@endsection

@push('scripts')

<script>
    $(document).ready(function() {
        // Function to check if any checkbox is selected
        function checkIfAnyCheckboxSelected() {
            return $('input[name="inspectore[]"]:checked').length > 0 || $('input[name="inspectorein[]"]:checked').length > 0;
        }
    
        // Disable submit button initially if no checkbox is checked
        $('#btn-submit').prop('disabled', !checkIfAnyCheckboxSelected());
    
        // Add change event listener to checkboxes
        $('input[name="inspectore[]"], input[name="inspectorein[]"]').on('change', function() {
            console.log(!checkIfAnyCheckboxSelected()); // Prevent form submission

            $('#btn-submit').prop('disabled', !checkIfAnyCheckboxSelected());
            if(!checkIfAnyCheckboxSelected() ){
                $('#inspectore-error').text('من فضلك اختر مفتش واحد على الأقل .');

            }else{
                $('#inspectore-error').text(''); // Clear error message on change

            }
        });    
    
        // Form submit event
        $('#add-form').on('submit', function(event) {
            if (!checkIfAnyCheckboxSelected()) {
                event.preventDefault();
                $('#inspectore-error').text('من فضلك اختر مفتش واحد على الأقل .');
            }
        });
    });
    </script>
@endpush
