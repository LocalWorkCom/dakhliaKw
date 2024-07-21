@extends('layout.header')
@section('content')

<section>
    <ol class="breadcrumb" dir="rtl">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="">الصلاحيات</a></li>
        <li class="breadcrumb-item active">تعديل صلاحية</li>
    </ol>
     
    <div class="container-fluid p-5" dir="rtl">
        <div class="row">
            <div class="col-lg-6">
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
                    <div class="p-5" dir="rtl">
                        @if(isset($permission))
                            <form action="{{ route('permissions.update', $permission->id) }}" method="post">
                            @method('PUT')
                        @else
                            <form action="{{ route('permissions.store') }}" method="post">
                        @endif
                            @csrf
                            @php
                                // This should be provided by the controller
                                // $permissionAction and $permissionModel
                            @endphp
                            <div class="form-group">
                                <h3>الصلاحية</h3>
                                {{-- <input type="text" class="form-control" name="name" value="{{ $permissionAction }}" required> --}}
                                <select class="custom-select custom-select-lg mb-3" name="name">
                                    <option selected disabled>اختر الصلاحية</option>
                                    <option value="view" {{ $permissionAction == 'view' ? 'selected' : '' }}>عرض</option>
                                    <option value="edit" {{ $permissionAction == 'edit' ? 'selected' : '' }}>تعديل</option>
                                    <option value="create" {{ $permissionAction == 'create' ? 'selected' : '' }}>اضافة</option>
                                    <option value="delete" {{ $permissionAction == 'delete' ? 'selected' : '' }}>ازالة</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <h3>القسم</h3>
                                <select class="custom-select custom-select-lg mb-3" name="model" required>
                                    <option selected disabled>اختر القسم</option>
                                    @foreach ($models as $item)
                                        <option value="{{ $item }}" {{ $permissionModel == $item ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">حفظ</button>
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
