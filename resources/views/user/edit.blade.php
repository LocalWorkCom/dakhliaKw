@extends('layout.main')
@section('content')

{{-- <body> --}}
  <section>
    <div class="row col-11" dir="rtl">
      <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
              <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
              <li class="breadcrumb-item"><a href="#">المستخدمين </a></li>
              <li class="breadcrumb-item active" aria-current="page"> <a href="#"> تعديل مستخدم</a></li>
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
                    <form>

                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input1"> الاسم</label>
                            <input type="text" id="input1" name="name" class="form-control" placeholder="الاسم" value="{{ $user->name  }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input2"> البريد الالكتروني</label>
                            <input type="text" id="input2" name="email" class="form-control" placeholder=" البريد الالكترونى" value="{{ $user->email  }}">
                        </div>
                        
                      </div>


                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input3"> الباسورد</label>
                            <input type="text" id="input3" name="password" class="form-control" placeholder="الباسورد" value="{{ $user->password  }}">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input4"> رقم المحمول</label>
                          <input type="text" id="input4" name="phone" class="form-control" placeholder=" رقم المحمول" value="{{ $user->phone  }}">
                        </div>
                      </div>


                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input5"> الوصف</label>
                            <textarea type="text" id="input5" name="description" class="form-control" placeholder="الوصف" rows="3" >{{ $user->description  }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input6">رقم العسكرى</label>
                          <input type="text" id="input6" name="military_number" class="form-control" placeholder="رقم العسكرى" value="{{ $user->military_number  }}">
                        </div>
                      </div>


                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input7"> الادوار</label>
                            <select id="input7" name="rule_id" class="form-control" placeholder="الادوار">
                              <option value=""></option>

                            </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input8">الوظيفة</label>
                          <input type="text" id="input8" name="job" class="form-control" placeholder="الوظيفة" value="{{ $user->job }}">
                        </div>
                      </div>


                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input9"> المسمي الوظيفي</label>
                            <input type="text" id="input9" name="job_title" class="form-control" placeholder="المسمي الوظيفي" value="{{ $user->job_title  }}">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input10">الجنسية</label>
                          <input type="text" id="input10" name="nationality" class="form-control" placeholder="الجنسية" value="{{ $user->nationality  }}">
                        </div>
                      </div>


                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input11">رقم المدنى</label>
                            <input type="text" id="input11" name="Civil_number" class="form-control" placeholder="رقم المدنى" value="{{ $user->Civil_number  }}">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input12">رقم الملف</label>
                          <input type="text" id="input12" name="file_number" class="form-control" placeholder="رقم الملف" value="{{ $user->file_number  }}">
                        </div>
                      </div>


                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                          <span>نعم : اختار مستخدم</span>
                          <span>/</span>
                          <span>لا : اختار موظف</span>
                          <label for="input13">هل يمكن لهذا لموظف ان يكون مستخدم ؟ </label>
                            <select id="input13" name="flag" class="form-control">
                              <option value="user">مستخدم</option>
                              <option value="employee">موظف</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input14">الاقدامية</label>
                          <input type="text" id="input14" name="seniority" class="form-control" placeholder="الاقدامية" value="{{ $user->seniority }}">
                        </div>
                      </div>


                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input15"> الادارة العامة</label>
                            <select id="input15" name="public_administration" class="form-control" placeholder="الادارة العامة">
                              <option value=""></option>
                              <option value=""></option>
                              <option value=""></option>
                              <option value=""></option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input16">موقع العمل</label>
                          <input type="text" id="input16" name="work_location" class="form-control" placeholder="موقع العمل" value="{{ $user->work_location }}">
                        </div>
                      </div>

                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input17">المنصب</label>
                            <input type="text" id="input17" name="position" class="form-control" placeholder="المنصب" value="{{ $user->position  }}">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input18">المؤهل</label>
                          <input type="text" id="input18" name="qualification" class="form-control" placeholder="المؤهل" value="{{ $user->qualification  }}">
                        </div>
                      </div>


                      
                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input19">تاريخ الميلاد</label>
                            <input type="date" id="input19" name="date_of_birth" class="form-control" placeholder="تاريخ الميلاد" value="{{ $user->date_of_birth  }}">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input20">تاريخ الالتحاق</label>
                          <input type="date" id="input20" name="joining_date" class="form-control" placeholder="تاريخ الالتحاق" value="{{ $user->joining_date  }}">
                        </div>
                      </div>


                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input21">العمر</label>
                            <input type="text" id="input21" name="age" class="form-control" placeholder="العمر" value="{{ $user->age  }}">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input22">مدة الخدمة</label>
                          <input type="date" id="input22" name="end_of_service" class="form-control" placeholder="مدة الخدمة " value="{{ $user->end_of_service  }}">
                        </div>
                      </div>


                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input24"> الرتبة</label>
                            <select id="input24" name="grade_id" class="form-control" placeholder="الرتبة">
                              <option value=""></option>
                              <option value=""></option>
                              <option value=""></option>
                              <option value=""></option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="input23">الصورة</label>
                          <input type="file" class="form-control" name="image" id="input23" placeholder="الصورة" value="{{ $user->work_location }}">
                        </div>
                      </div>



                      <div class="form-row mx-2 mt-4">
                        <div class="form-group col-md-6">
                            <label for="input25"> القسم</label>
                            <select id="input25" name="department_id" class="form-control" placeholder="القسم">
                              <option value=""></option>
                              <option value=""></option>
                              <option value=""></option>
                              <option value=""></option>
                            </select>
                        </div>
                      </div>
                      </form>
                </div>
            </div>
        </div>
        

   
  </section>

    
  @endsection
