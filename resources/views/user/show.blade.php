@extends('layout.main')
@section('content')
@section('title')
    تفاصيل المستخدم
@endsection
{{-- <body> --}}
<section>
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>

                @if ($user->flag == 'user')
                    <li class="breadcrumb-item"><a href="{{ route('user.index', 0) }}">المستخدمين</a></li>
                @elseif ($user->flag == 'employee')
                    <li class="breadcrumb-item"><a href="{{ route('user.employees', 1) }}">الموظفين</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> عرض </a></li>
            </ol>

        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            @if ($user->flag == 'user')
                <p>المستخدمين</p>
            @elseif ($user->flag == 'employee')
                <p>الموظفين</p>
            @endif
        </div>
    </div>




    <div class="row">
        <div class="container  col-11 mt-4 p-0 ">


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

            <!-- toqa-table-show -->


            <div class="form-row mx-3 mb-3 pt-5 pt-5">

                <table class="table table-bordered" dir="rtl">
                    <tbody>
                        <tr>
                            <th scope="row" style="    background-color: #f5f6fa;"> الاسم</th>

                            <td style="    background-color: #f5f6fa;">{{ $user->name }} </td>
                        </tr>

                        <tr>
                            <th scope="row"> البريد الالكترونى</th>
                            <td>{{ $user->email }} </td>
                        </tr>
                        <tr>
                            <th scope="row"> رقم المحمول </th>
                            <td>
                                {{ $user->phone }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"> الوصف </th>
                            <td> {{ $user->description }} </td>
                        </tr>
                        <tr>
                            <th scope="row"> رقم العسكري</th>
                            <td> {{ $user->military_number }} </td>
                        </tr>

                        <tr>
                            <th scope="row">الوظيفة</th>
                            @foreach ($job as $item)
                                @if ($user->job_id == $item->id)
                                <td>   {{ $item->name }} </td>
                                @endif
                            @endforeach

                        </tr>


                        <tr>
                            <th scope="row"> المسمي الوظيفي </th>
                            <td> {{ $user->job_title }} </td>
                        </tr>

                        <tr>
                            <th scope="row"> الجنسيه </th>
                            <td> {{ $user->nationality }} </td>
                        </tr>
                        <tr>
                            <th scope="row"> الرقم المدني </th>
                            <td> {{ $user->Civil_number }} </td>
                        </tr>
                        <tr>
                            <th scope="row"> المؤهل </th>
                            <td> {{ $user->qualification }} </td>
                        </tr>
                        <tr>
                            <th scope="row"> تاريخ الميلاد </th>
                            <td> {{ $user->date_of_birth }} </td>
                        </tr>
                        <tr>
                            <th scope="row"> تاريخ الالتحاق </th>
                            <td> {{ $user->joining_date }} </td>
                        </tr>
                        <tr>
                            <th scope="row"> مدة الخدمه </th>
                            <td> {{ $end_of_service }} </td>
                        </tr>
                        <tr>
                            <th scope="row"> الرتبه </th>
                            @foreach ($grade as $item)
                                <!--<option value="{{ $item->id }}"> -->
                                <!--    <td > {{ $item->name }}</td>-->
                                <!--</option>-->

                                @if ($user->grade_id == $item->id)
                                    <td>
                                        {{ $item->name }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        <tr>
                            <th scope="row"> رقم الملف </th>
                            <td> {{ $user->file_number }} </td>
                        </tr>

                        {{-- <tr>
                            <th scope="row" >    الباسورد  </th>
                            <td  type="password" name="password" id="password" > {{ $user->password }} </td>
                        </tr> --}}

                        <tr>
                            <th scope="row">المهام</th>
                            <td>
                                <!-- Display the selected item name here with a null check -->
                                @php
                                    $selectedRule = $rule->firstWhere('id', $user->rule_id);
                                @endphp

                                @if ($selectedRule)
                                    {{ $selectedRule->name }}
                                @else
                                    <!-- Fallback text if no rule is found -->
                                    لم يتم العثور على المهمة
                                @endif
                            </td>
                            <td style="display: none;">
                                <select id="input7" name="rule_id" class="form-control" placeholder="المهام"
                                    disabled>
                                    @foreach ($rule as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $user->rule_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>


                        <tr>
                            <th scope="row"> يصنف المستخدم ك </th>

                            @if ($user->flag == 'user')
                                <td> مستخدم </td>
                            @else
                                <td> موظف </td>
                            @endif
                        </tr>
                        <tr>
                            <th scope="row"> الاقدامية </th>
                            <td> {{ $user->seniority }} </td>
                        </tr>

                        <tr>
                            <th scope="row">الإدارة العامة</th>
                            <td>
                                <!-- Display the selected item name here with a null check -->
                                @php
                                    $selectedDepartment = $department->firstWhere('id', $user->public_administration);
                                @endphp

                                @if ($selectedDepartment)
                                    {{ $selectedDepartment->name }}
                                @else
                                    <!-- Fallback text if no department is found -->
                                    لم يتم العثور على الإدارة
                                @endif
                            </td>
                            <td style="display: none;">
                                <select id="input7" name="public_administration" class="form-control"
                                    placeholder="المهام" disabled>
                                    @foreach ($department as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $user->public_administration == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"> موقع العمل </th>
                            <td> {{ $user->work_location }} </td>
                        </tr>

                        @if ($user->image)
                            <tr>
                                <th scope="row">الصوره</th>

                                <td>
                                    <div class="row">
                                        <div class="col-md-11 mb-3 px-5 mt-2">
                                            <a href="#" class="image-popup" data-toggle="modal"
                                                data-target="#imageModal" data-image="{{ asset($user->image) }}"
                                                data-title="{{ $user->image }}">
                                                <img src="{{ asset($user->image) }}" class="img-thumbnail mx-2"
                                                    alt="{{ $user->image }}">
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif




                        {{-- <tr>
                            <th scope="row" >    الصوره  </th>
                            <td >
                                <div class="row">
                                    <div class="col-md-11 mb-3 px-5 mt-2">
                                        <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                                            data-image="{{ asset($user->image) }}" data-title="{{ $user->image }}">
                                            <img src="{{ asset($user->image) }}" class="img-thumbnail mx-2"
                                                alt="{{ $user->image }}">
                                        </a>

                                    </div>

                                </div>
                                <div class="form-group col-md-6">
                                    <label for="input23">الصورة</label>
                                    <input type="file" class="form-control" name="image" id="input23"
                                        placeholder="الصورة" value="{{  }}">
                                </div>
                            </td>
                        </tr> --}}

                    </tbody>
                </table>
            </div>



            <!-- enddddddddd of datashow table toqa -->



            {{-- <div class="p-5">

                    <div class="form-row mx-2 mt-4 d-flex flex-row-reverse">
                        <div class="form-group col-md-6">
                            <label for="input1"> الاسم</label>
                            <input type="text" id="input1" name="name" class="form-control" placeholder="الاسم"
                                value="{{ $user->name }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input2"> البريد الالكتروني</label>
                            <input type="text" id="input2" name="email" class="form-control"
                                placeholder=" البريد الالكترونى" value="{{ $user->email }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input4"> رقم المحمول</label>
                            <input type="text" id="input4" name="phone" class="form-control"
                                placeholder=" رقم المحمول" value="{{ $user->phone }}" disabled>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="input5"> الوصف</label>
                            <textarea type="text" id="input5" name="description" class="form-control" placeholder="الوصف" rows="3"
                                disabled>{{ $user->description }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input6">رقم العسكرى</label>
                            <input type="text" id="input6" name="military_number" class="form-control"
                                placeholder="رقم العسكرى" value="{{ $user->military_number }}" disabled>
                        </div>


                        <div class="form-group col-md-6">
                            <label for="input8">الوظيفة</label>
                                <select class="custom-select custom-select-lg mb-3" name="job" id="job" disabled>
                                    <option selected disabled>Open this select menu</option>
                                    @foreach ($job as $item)
                                    <option value="{{ $item->id }}" {{ $user->job == $item->id ? 'selected' : ''}}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="input9"> المسمي الوظيفي</label>
                            <input type="text" id="input9" name="job_title" class="form-control"
                                placeholder="المسمي الوظيفي" value="{{ $user->job_title }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input10">الجنسية</label>
                            <input type="text" id="input10" name="nationality" class="form-control"
                                placeholder="الجنسية" value="{{ $user->nationality }}" disabled>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="input11">رقم المدنى</label>
                            <input type="text" id="input11" name="Civil_number" class="form-control"
                                placeholder="رقم المدنى" value="{{ $user->Civil_number }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input12">رقم الملف</label>
                            <input type="text" id="input12" name="file_number" class="form-control"
                                placeholder="رقم الملف" value="{{ $user->file_number }}" disabled>
                        </div>

                        @if ($user->flag == 'user')
                            <div class="form-group col-md-6">
                                <label for="input3"> الباسورد</label>
                                <input type="password" id="input3" name="password" class="form-control"
                                    placeholder="الباسورد" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="input7"> المهام</label>
                                <select id="input7" name="rule_id" class="form-control" placeholder="المهام"
                                    disabled>
                                    @foreach ($rule as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $user->rule_id == $item->id ? 'selected' : '' }}> {{ $item->name }}
                                        </option>
                                    @endforeach


                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="input25"> القسم</label>
                                <select id="input25" name="department_id" class="form-control" placeholder="القسم"
                                disabled>
                                    @foreach ($department as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $user->department_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach

                                </select>
                            </div>
                        @endif

                        <div class="form-group col-md-6">
                            <label for="input13">هل يمكن لهذا لموظف ان يكون مستخدم ؟ </label>
                            <select id="input13" name="flag" class="form-control" disabled>
                                @if ($user->flag == 'user')
                                    <option value="user" selected>مستخدم</option>
                                    <option value="employee">موظف</option>
                                @else
                                    <option value="user">مستخدم</option>
                                    <option value="employee" selected>موظف</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input14">الاقدامية</label>
                            <input type="text" id="input14" name="seniority" class="form-control"
                                placeholder="الاقدامية" value="{{ $user->seniority }}" disabled>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="input15"> الادارة العامة</label>
                            <select id="input15" name="public_administration" class="form-control"
                                placeholder="الادارة العامة" disabled>
                                @foreach ($department as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $user->public_administration == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input16">موقع العمل</label>
                            <input type="text" id="input16" name="work_location" class="form-control"
                                placeholder="موقع العمل" value="{{ $user->work_location }}" disabled>
                        </div>

                        {{-- <div class="form-group col-md-6">
                            <label for="input17">المنصب</label>
                            <input type="text" id="input17" name="position" class="form-control" placeholder="المنصب" value="{{ $user->position  }}">
                        </div> --}}
            {{-- <div class="form-group col-md-6">
                            <label for="input18">المؤهل</label>
                            <input type="text" id="input18" name="qualification" class="form-control"
                                placeholder="المؤهل" value="{{ $user->qualification }}" disabled>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="input19">تاريخ الميلاد</label>
                            <input type="date" id="input19" name="date_of_birth" class="form-control"
                                placeholder="تاريخ الميلاد" value="{{ $user->date_of_birth }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input20">تاريخ الالتحاق</label>
                            <input type="date" id="input20" name="joining_date" class="form-control"
                                placeholder="تاريخ الالتحاق" value="{{ $user->joining_date }}" disabled>
                        </div> --}}

            {{-- <div class="form-group col-md-6">
                            <label for="input21">العمر</label>
                            <input type="text" id="input21" name="age" class="form-control" placeholder="العمر" value="{{ $user->age  }}">
                        </div> --}}
            {{-- <div class="form-group col-md-6">
                            <label for="input22">مدة الخدمة</label>
                            <input type="date" id="input22" name="end_of_service" class="form-control"
                                placeholder="مدة الخدمة " value="{{ $end_of_service }}" disabled>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="input24"> الرتبة</label>
                            <select id="input24" name="grade_id" class="form-control" placeholder="الرتبة" disabled>
                                @foreach ($grade as $item)
                                    <option value="{{ $item->id }}"> {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-11 mb-3 px-5 mt-2">
                                <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                                    data-image="{{ asset($user->image) }}" data-title="{{ $user->image }}">
                                    <img src="{{ asset($user->image) }}" class="img-thumbnail mx-2"
                                        alt="{{ $user->image }}">
                                </a>

                            </div>

                        </div> --}}
            {{-- <div class="form-group col-md-6">
                            <label for="input23">الصورة</label>
                            <input type="file" class="form-control" name="image" id="input23"
                                placeholder="الصورة" value="{{  }}">
                        </div> --}}

            {{-- </div> --}}
            <!-- Save button -->

            {{-- </div> --}}
        </div>
    </div>
    {{-- Modal for Image Popup --}}
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">عرض الصورة</h5>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> --}}
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="#" class="img-fluid" alt="صورة">
                </div>
            </div>
        </div>
    </div>



</section>


@endsection
@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('.image-popup').click(function(event) {
            event.preventDefault();
            var imageUrl = $(this).data('image');
            var imageTitle = $(this).data('title');

            // Set modal image and title
            $('#modalImage').attr('src', imageUrl);
            $('#imageModalLabel').text(imageTitle);

            // Show the modal
            $('#imageModal').modal('show');
        });
    });
</script>
@endpush
