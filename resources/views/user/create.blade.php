@extends('layout.main')
@section('content')

<div class="row col-11" dir="rtl">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
            <li class="breadcrumb-item"><a href="#">المستخدمين </a></li>
            <li class="breadcrumb-item active" aria-current="page"> <a href="#"> اضافه مستخدم</a></li>
        </ol>
    </nav>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> المستخـــــــــــدمين </p>
    </div>
</div>
<br>

<div class="row">
    <div class="container  col-11 mt-3 p-0 ">
       
            
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
                <div class="p-5">



                    <form action="{{ route('user.store') }}" method="post" class="text-right">
                        @csrf

                        <input type="hidden" name="type" value="{{ $flag }}">
                        <div class="form-row mx-2 mt-4">
                            <div class="form-group col-md-6">
                                <label for="nameus"> الاسم</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="job"> الوظيفة</label>
                                <input type="text" id="job" name="job" class="form-control" required>
                            </div>
                            
                        </div>
                        <div class="form-row mx-2">
                            <div class="form-group col-md-6">
                                <label for="military_number">رقم العسكرى</label>
                                <input type="text" id="military_number" name="military_number" class="form-control"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone">رقم المحمول</label>
                                <input type="text" id="phone" name="phone" class="form-control" required>
                            </div>

                        </div>
                        <div class="form-row mx-2">
                            <div class="form-group col-md-6">
                                <label for="filenum">رقم الملف</label>
                                <input type="text" id="filenum" name="file_number" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="department">الادارة</label>
                                <select class="custom-select custom-select-lg mb-3" name="department" id="department">
                                    <option selected>Open this select menu</option>
                                    @foreach ($alldepartment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        @if ($flag == "0")
                        <div class="form-row mx-2">
                            <div class="form-group col-md-6">
                                <label for="rule_id">الادوار</label>
                                <select class="custom-select custom-select-lg mb-3" name="rule" id="rule_id">
                                    <option selected>Open this select menu</option>
                                    @foreach ($rule as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="Civil_number">الباسورد</label>
                                <input type="text" id="password" name="password" class="form-control" required>

                            </div>
                        </div>
                        @else
                        <div class="form-row mx-2">
                            <div class="form-group col-md-6" >
                                <input type="checkbox" class="form-check-input" id="myCheckbox" name="solder">
                                <label class="form-check-label" for="myCheckbox">عسكرى</label>
                            </div>
                        </div>
                        <div class="form-row mx-2">
                            <div class="form-group col-md-6" id="grade" style="display: none;">
                                <label for="grade_id">الرتبة</label>
                                <select class="custom-select custom-select-lg mb-3" name="grade_id" id="grade_id">
                                    <option selected>Open this select menu</option>
                                    {{-- @foreach ($rule as $item) --}}
                                    {{-- <option value="{{ $item->id }}">{{ $item->name }}</option> --}}
                                    <option value=""></option>
                                    {{-- @endforeach --}}
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="date_of_birth">تاريخ الميلاد</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" >

                            </div>
                            <div class="form-group col-md-6">
                                <label for="image">الصورة</label>
                                <input type="file" id="image" name="image" class="form-control" >

                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">وصف</label>
                                <textarea class="form-control" id="description" name="description" placeholder="الوصف" rows="3"></textarea>
                                {{-- <input type="file" id="image" name="image" class="form-control" required> --}}

                            </div>
                        </div>
                        @endif

                        



                        <!-- Save button -->
                        <div class="container col-12 ">
                            <div class="form-row mt-4 mb-5">
                                <button type="submit" class="btn-blue">حفظ</button>
                            </div>
                        </div>
                        <br>
                    </form>

                
         
        </div>

    </div>

</div>


</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const checkbox = document.getElementById("myCheckbox");
    const grade = document.getElementById("grade");

            checkbox.addEventListener("change", function() {
                if (checkbox.checked) {
                    grade.style.display = "block";
                } else {
                    grade.style.display = "none";
                }
            
    });
});
</script>


@endsection