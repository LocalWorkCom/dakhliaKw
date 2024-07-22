

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
         
            </div>
        </div>
    </div>
@endsection

