@extends('layout.main')
@section('content')

    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="#">المهام </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="#"> اضافه مهمه</a></li>
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
            <div class="p-5">



                <form action="{{ route('rule.store') }}" method="post" class="text-right">
                    @csrf

                    <div class="form-row mx-2 mt-4">

                        <div class="form-group col-md-6">
                            <label for="nameus"> الدور</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="department">الادارة</label>
                            <select class="custom-select custom-select-lg mb-3" name="department_id" id="department_id">
                                <option selected>Open this select menu</option>
                                @foreach ($alldepartment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="department">الصلاحية</label>
                            @foreach ($allPermission as $item)
                                <div class="form-check">
                                    <input type="checkbox" id="exampleCheck1" value="{{ $item->id }}"
                                        name="permissions_ids[]" class="form-control" required>
                                    <label class="form-check-label" for="exampleCheck1">{{ $item->name }}</label>
                                </div>
                            @endforeach
                            </select>
                        </div>

                    </div>

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
    {{-- <div class="col-lg-6">
                <div class="bg-white p-5">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
                </div>
            </div> --}}
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
