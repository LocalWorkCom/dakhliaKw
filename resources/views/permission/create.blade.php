@extends('layout.main')
@section('content')

<section>
    <ol class="breadcrumb" dir="rtl">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('permission.index') }}">الصلاحيات  </a></li>

        <li class="breadcrumb-item active"> اضافه صلاحية</li>

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
                    <div class="p-5" dir="rtl">
    
                        
    
                        <form action="{{ route('permission.store') }}" method="post">
                            @csrf
                            <div class="form-group ">
                                <label>الصلاحية</label>
                                <select class="custom-select custom-select-lg mb-3" name="name" multiple>
                                    <option selected>Open this select menu</option>
                                    <option value="view">عرض</option>
                                    <option value="edit">تعديل</option>
                                    <option value="create">اضافة</option>
                                    {{-- <option value="delete">ازالة</option> --}}
                                  </select>
                            </div>
                            <div class="form-group">
                                <label>القسم</label>
                                <select class="custom-select custom-select-lg mb-3" name="model">
                                    <option selected>Open this select menu</option>
                                    @foreach ($models as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                  </select>
                            </div>
                            
                            <button type="submit" class="btn-all mt-2">Submit</button>
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

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush