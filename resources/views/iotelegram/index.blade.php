@extends('layout.header')

@section('title', 'الواردات')

@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('iotelegrams.add') }}" class="btn btn-primary mt-3">إضافة جديد</a>
        </div>
        <div class="card">
            <div class="card-header">الواردات</div>

            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="global_search" class="form-control" placeholder="بحث ...">
                </div>
                {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
