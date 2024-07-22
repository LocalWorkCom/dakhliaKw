

@extends('layout.main')

@section('title', 'الادارات')

@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('sub_departments.create') }}" class="btn btn-primary mt-3">إضافة قسم</a>
        </div>
        
        <div class="card">
            <div class="card-header">الاقسام</div>

            <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
