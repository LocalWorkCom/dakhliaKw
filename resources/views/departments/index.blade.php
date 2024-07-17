

@extends('layout.header')

@push('style')
{{-- <script src="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css"></script> --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
<section style="direction: rtl;">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('departments.index') }}">الادارات</a></li>
    </ol>
    
    <div class="container-fluid" style="text-align: center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-block">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>

   
</section>
@endsection

@push('javascripts')
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
{{ $dataTable->scripts() }}
@endpush
