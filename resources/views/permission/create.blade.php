@extends('layout.main')
@section('content')
@section('title')
    اضافة
@endsection
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('permission.index') }}">الصلاحيات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="#"> اضافه صلاحية</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> الصلاحيات </p>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="container col-11 mt-3 p-0 " >


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
                
                <form action="{{ route('permission.store') }}" method="post" class="text-right">
                    @csrf

                    <div class="container col-10 mt-4 p-5" style="border:0.5px solid #C7C7CC;">
                        <div class="form-row mx-auto d-flex flex-row-reverse" >
                       
                                <div class="form-group col-md-6 ">
                                    <label for="filenum">الصلاحية</label>
                                    <select class="custom-select custom-select-lg mb-3" name="name" id="name">
                                        <option selected>Open this select menu</option>
                                        <option value="view">عرض</option>
                                        <option value="edit">تعديل</option>
                                        <option value="create">اضافة</option>
                                        {{-- <option value="delete">ازالة</option> --}}
                                    </select>
    
                                </div>
                      
                            
                            
                            <div class="form-group col-md-6">
                                <label for="model">القسم</label>
                                <select class="custom-select custom-select-lg mb-3 " name="model" id="model">
                                    <option selected>Open this select menu</option>
                                    @foreach ($models as $item)
                                        <option value="{{ $item}}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Save button -->
                    {{-- <div class="container col-12 ">
                        <div class="form-row mt-4 mb-5">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </div> --}}
                    <div class="container col-10 ">
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
