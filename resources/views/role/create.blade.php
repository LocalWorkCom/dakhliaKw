@extends('layout.header')
@section('content')

<section>
    <ol class="breadcrumb" dir="rtl">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="">الصلاحيات  </a></li>

        <li class="breadcrumb-item active"> اضافه صلاحية</li>

    </ol>
     
    <div class="container-fluid p-5"  >
        <div class="row">
            <div class="col-lg-7 offset-2">
                <div class="bg-white">
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
    
                        
    
                        <form action="{{ route('rule.store') }}" method="post">
                            @csrf
                            <div class="form-group ">
                                <h3>الدور</h3>
                                <input type="text" name="name" id="" style="width: 100%">
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
                                    @foreach ($allPermission as $item)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1" value="{{ $item->id }}" name="permissions_ids[]">
                                        <label class="form-check-label" for="exampleCheck1">{{ $item->name }}</label>
                                      </div>
                                    @endforeach
                                  {{-- </select> --}}    
                            </div>
                           
                            
                            <button type="submit" class="btn btn-primary">Submit</button>
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

    
@endsection

{{-- @push('scripts')
    {{ $dataTable->scripts() }}
@endpush --}}