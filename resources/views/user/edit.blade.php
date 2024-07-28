@extends('layout.main')
@section('title')
تعديل
@endsection
@section('content')


<section>
    <div class="row col-11" dir="rtl">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>

              @if ($user->flag == "user")
              <li class="breadcrumb-item"><a href="{{ route('user.index', 0) }}">المستخدمين</a></li>

              @elseif ($user->flag == "employee")
              <li class="breadcrumb-item"><a href="{{ route('user.employees', 1) }}">الموظفين</a></li>

              @endif
          <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل </a></li>
      </ol>
         
      </nav>
  </div>
  <div class="row ">
    <div class="container welcome col-11">
        @if ($user->flag == "user")
        <p>المستخدمين</p>

        @elseif ($user->flag == "employee")
        <p>الموظفين</p>

        @endif
    </div>
</div>
  
       
    </div>

    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="container col-10 mt-1 mb-5 pb-5 pt-5 mt-5" style="border:0.5px solid #C7C7CC;">

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
                {{-- {{ dd($user) }} --}}
                <div class="">
                    <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input1"> الاسم</label>
                                <input type="text" id="input1" name="name" class="form-control" placeholder="الاسم"
                                    value="{{ $user->name  }}">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input2"> البريد الالكتروني</label>
                                <input type="text" id="input2" name="email" class="form-control"
                                    placeholder=" البريد الالكترونى" value="{{ $user->email  }}">
                            </div>
                        </div>

                        <div class="form-row mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input4"> رقم المحمول</label>
                                <input type="text" id="input4" name="phone" class="form-control"
                                    placeholder=" رقم المحمول" value="{{ $user->phone  }}">
                            </div>

                            <div class="form-group col-md-5 mx-2">
                                <label for="input6">رقم العسكرى</label>
                                <input type="text" id="input6" name="military_number" class="form-control"
                                    placeholder="رقم العسكرى" value="{{ $user->military_number  }}">
                            </div>
                        </div>

                        <div class="form-row  mx-2 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-10 mx-2">
                                <label for="input8">الوظيفة</label>
                                <select class="custom-select custom-select-lg mb-3" name="job" id="job">
                                    <option selected disabled>Open this select menu</option>
                                    @foreach ($job as $item)
                                    <option value="{{ $item->id }}" {{ $user->job == $item->id ? 'selected' : ''}}>
                                        {{ $item->name }}
                                    </option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" id="input8" name="job" class="form-control"
                                        placeholder="الوظيفة" value="{{ $user->job }}"> --}}
                            </div>
                        </div>

                        <div class="form-row  mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input9"> المسمي الوظيفي</label>
                                <input type="text" id="input9" name="job_title" class="form-control"
                                    placeholder="المسمي الوظيفي" value="{{ $user->job_title  }}">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input10">الجنسية</label>
                                <input type="text" id="input10" name="nationality" class="form-control"
                                    placeholder="الجنسية" value="{{ $user->nationality  }}">
                            </div>
                        </div>

                        <div class="form-row  mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input11">رقم المدنى</label>
                                <input type="text" id="input11" name="Civil_number" class="form-control"
                                    placeholder="رقم المدنى" value="{{ $user->Civil_number  }}">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input12">رقم الملف</label>
                                <input type="text" id="input12" name="file_number" class="form-control"
                                    placeholder="رقم الملف" value="{{ $user->file_number  }}">
                            </div>
                        </div>

                        @if ($user->flag == "user")
                        <div class="form-row  mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input3"> الباسورد</label>
                                <input type="password" id="input3" name="password" class="form-control"
                                    placeholder="الباسورد">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input7"> المهام</label>
                                <select id="input7" name="rule_id" class="form-control" placeholder="المهام">
                                    @foreach ($rule as $item)
                                    <option value="{{ $item->id }}" {{ $user->rule_id == $item->id ? 'selected' : ''}}>
                                        {{ $item->name }}</option>
                                    @endforeach


                                </select>
                            </div>
                        </div>
                        <div class="form-row mx-2  d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-10 mx-2">
                                <label for="input25"> القسم</label>
                                <select id="input25" name="department_id" class="form-control" placeholder="القسم">
                                    @foreach ($department as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $user->department_id == $item->id ? 'selected' : ''}}> {{ $item->name }}
                                    </option>
                                    @endforeach

                                </select>
                            </div> </div>
                            @endif

                            <div class="form-row mx-2 mx-2 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-10">
                                    <label for="input13">هل يمكن لهذا لموظف ان يكون مستخدم ؟ </label>
                                    {{-- <span>نعم : اختار مستخدم</span>
                                    <span>/</span>
                                    <span>لا : اختار موظف</span> --}}

                                    <select id="input13" name="flag" class="form-control">
                                        @if ($user->flag == "user")
                                        <option value="user" selected>مستخدم</option>
                                        <option value="employee">موظف</option>
                                        @else
                                        <option value="user">مستخدم</option>
                                        <option value="employee" selected>موظف</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            </div>

                        <div class="form-row  mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input14">الاقدامية</label>
                                <input type="text" id="input14" name="seniority" class="form-control"
                                    placeholder="الاقدامية" value="{{ $user->seniority }}">
                            </div>

                            <div class="form-group col-md-5 mx-2">
                                <label for="input15"> الادارة العامة</label>
                                <select id="input15" name="public_administration" class="form-control"
                                    placeholder="الادارة العامة">
                                    @foreach ($department as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $user->public_administration == $item->id ? 'selected' : ''}}>
                                        {{ $item->name }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        <div class="form-row mx-2 mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input16">موقع العمل</label>
                                <input type="text" id="input16" name="work_location" class="form-control"
                                    placeholder="موقع العمل" value="{{ $user->work_location }}">
                            </div>

                            {{-- <div class="form-group col-md-5 mx-2">
                                    <label for="input17">المنصب</label>
                                    <input type="text" id="input17" name="position" class="form-control"
                                        placeholder="المنصب" value="{{ $user->position  }}">
                            </div> --}}
                            <div class="form-group col-md-5 mx-2">
                                <label for="input18">المؤهل</label>
                                <input type="text" id="input18" name="qualification" class="form-control"
                                    placeholder="المؤهل" value="{{ $user->qualification  }}">
                            </div>
                        </div>

                        <div class="form-row mx-2 mx-3 d-flex justify-content-center flex-row-reverse">
                            <div class="form-group col-md-5 mx-2">
                                <label for="input19">تاريخ الميلاد</label>
                                <input type="date" id="input19" name="date_of_birth" class="form-control"
                                    placeholder="تاريخ الميلاد" value="{{  $user->date_of_birth  }}">
                            </div>
                            <div class="form-group col-md-5 mx-2">
                                <label for="input20">تاريخ الالتحاق</label>
                                <input type="date" id="input20" name="joining_date" class="form-control"
                                    placeholder="تاريخ الالتحاق" value="{{ $user->joining_date  }}">
                            </div>
                        </div>
                {{-- <div class="form-group col-md-5 mx-2">
                                    <label for="input21">العمر</label>
                                    <input type="text" id="input21" name="age" class="form-control" placeholder="العمر"
                                        value="{{ $user->age  }}">
            </div> --}}

            <div class="form-row mx-2 mx-3 d-flex justify-content-center flex-row-reverse">
                <div class="form-group col-md-5 mx-2">
                    <label for="input22">مدة الخدمة</label>
                    <input type="date" id="input22" name="end_of_service" class="form-control" placeholder="مدة الخدمة "
                        value="{{ $end_of_service }}">
                </div>

                <div class="form-group col-md-5 mx-2">
                    <label for="input24"> الرتبة</label>
                    <select id="input24" name="grade_id" class="form-control" placeholder="الرتبة">
                        @foreach ($grade as $item)
                        <option value="{{ $item->id }}"> {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row mx-2 mx-2 d-flex justify-content-center flex-row-reverse">
                <div class="form-group col-md-10">
                    <label for="input5"> الوصف</label>
                    <textarea type="text" id="input5" name="description" class="form-control" placeholder="الوصف"
                        rows="3">{{ $user->description  }}</textarea>
                </div>
            </div>
            <div class="form-row mx-2 mx-2 d-flex justify-content-center flex-row-reverse">
                <div class="form-group col-md-10">
                    <label for="input23">الصورة</label>
                    <input type="file" class="form-control" name="image" id="input23" placeholder="الصورة"
                        value="{{ $user->image }}">
                </div>
            </div>

        </div>

        <!-- Save button -->
        <div class="container col-10 mt-5 mb-5 ">
            <div class="form-row col-10 " dir="ltr">
                <button class="btn-blue " type="submit">
                    اضافة </button>
            </div>
        </div>
        <br>
        </form>
    </div>
    </div>
    </div>
    </div>
    </div>

</section>


@endsection