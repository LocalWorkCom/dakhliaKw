@extends('layout.main')
@section('content')

    {{-- <body> --}}
    <section>
        <div class="row col-11" dir="rtl">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                    <li class="breadcrumb-item"><a href="#">المهام </a></li>
                    <li class="breadcrumb-item active" aria-current="page"> <a href="#"> تعديل المهام</a></li>
                </ol>
            </nav>
        </div>
        <div class="row ">
            <div class="container welcome col-11">
                <p> المهام </p>
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
                {{-- {{ dd($user) }} --}}
                <div class="p-5">
                    <form action="{{ route('rule_update', $rule_permission->id) }}" method="POST">
                        @csrf

                        <div class="form-group col-md-6">
                            <label for="input8">الدور</label>
                            <input type="text" id="input8" name="name" class="form-control" placeholder="الوظيفة"
                                value="{{ $rule_permission->name }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="input25"> القسم</label>
                            <select id="input25" name="department_id" class="form-control" placeholder="القسم">
                                @foreach ($alldepartment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="input25"> الصلاحية</label>
                            @if ($rule_permission->name == 'admin')
                                @foreach ($allpermission as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <h3>الصلاحية</h3>
                            {{-- <select class="custom-select custom-select-lg mb-3" name="model"> --}}
                            {{-- <option selected>Open this select menu</option> --}}
                            @if ($rule_permission->name == 'admin')
                                @foreach ($allpermission as $item)
                                    {{-- @else
                                        @foreach ($hisPermissions as $item)
                                     --}}
                                    <div class="form-check">
                                        <input type="checkbox" class="form-control" id="exampleCheck1"
                                            value="{{ $item->id }}" name="permissions_ids[]" checked>
                                        <label for="exampleCheck1">{{ $item->name }}</label>

                                    </div>
                                @endforeach
                            @endif
                            {{-- </select> --}}
                        </div>


                        <!-- Save button -->
                        <div class="container col-12 ">
                            <div class="form-row mt-4 mb-5">
                                <button type="submit" class="btn-blue">حفظ</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        </div>
        </div>

    </section>


@endsection
