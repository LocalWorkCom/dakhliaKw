@extends('layout.main')
@section('content')

<section>
    <ol class="breadcrumb" dir="rtl">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="">الادوار</a></li>
        <li class="breadcrumb-item active">تعديل صلاحية</li>
    </ol>
     
    <div class="container-fluid p-5">
        <div class="row">
            <div class="col-lg-7 offset-2">
                <div class="bg-white">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="p-5">
                        <form action="{{ route('rule_update', $rule_permission->id) }}" method="post">
                            @csrf
                            <div class="form-group ">
                                <h3>الدور</h3>
                                <input type="text" name="name" value={{ $rule_permission->name }} style="width: 100%">
                            </div>
                            <div class="form-group">
                                <h3>الاداره</h3>
                                <select class="custom-select custom-select-lg mb-3" name="department_id">
                                    <option selected>Open this select menu</option>
                                    @foreach ($alldepartment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <h3>الصلاحية</h3>
                                {{-- <select class="custom-select custom-select-lg mb-3" name="model"> --}}
                                    {{-- <option selected>Open this select menu</option> --}}
                                    @if ($rule_permission->name == "admin")
                                        @foreach ($allpermission as $item)
                                    {{-- @else
                                        @foreach ($hisPermissions as $item)
                                     --}}
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1" value="{{ $item->id }}" name="permissions_ids[]" checked>
                                        <label class="form-check-label" for="exampleCheck1">{{ $item->name }}</label>
                                      </div>
                                    @endforeach
                                    @endif
                                  {{-- </select> --}}    
                            </div>
                           
                            
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection


