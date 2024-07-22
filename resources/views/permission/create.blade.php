@extends('layout.header')
@section('content')

<section>
    <ol class="breadcrumb" dir="rtl">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="">الصلاحيات  </a></li>

        <li class="breadcrumb-item active"> اضافه صلاحية</li>

    </ol>
     
    <div class="container-fluid p-5" dir="rtl" >
        <div class="row">
            <div class="col-lg-6">
                <div class="bg-white">
                    @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                    <div class="p-5" dir="rtl">
    
                        
    
                        <form action="{{ route('permission.store') }}" method="post">
                            @csrf
                            <div class="form-group ">
                                <h3>الصلاحية</h3>
                                <select class="custom-select custom-select-lg mb-3" name="name">
                                    <option selected>Open this select menu</option>
                                    <option value="view">عرض</option>
                                    <option value="edit">تعديل</option>
                                    <option value="create">اضافة</option>
                                    {{-- <option value="delete">ازالة</option> --}}
                                  </select>
                            </div>
                            <div class="form-group">
                                <h3>القسم</h3>
                                <select class="custom-select custom-select-lg mb-3" name="model">
                                    <option selected>Open this select menu</option>
                                    @foreach ($models as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                  </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit</button>
                          </form>
                            
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-white p-5">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
                </div>
            </div>
        </div>
        
    </div>

   
</section>

    
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush