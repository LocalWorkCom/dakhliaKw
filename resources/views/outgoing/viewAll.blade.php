@extends('layout.main')


@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('Export.create') }}" class="btn btn-primary mt-3">إضافة جديد</a>
        </div>
        <h2>الصادرات</h2>

           
                <div class="mb-3">
                    @include('inc.flash')
                    {{-- <input type="text" id="global_search" class="form-control" placeholder="بحث ..."> --}}
                </div>
                {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
    </div>
       
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush
