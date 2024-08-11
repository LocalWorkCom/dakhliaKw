@extends('layout.main')
@push('style')
@endpush
@section('title')
    القطاعات
@endsection
@section('content')
@foreach ($groups_points as  $groups_point)
{{-- {{ dd($groups_point) }} --}}
<div class="row" dir="rtl">
   {{ $groups_point}}
</div>
@endforeach
   
    @endsection
    @push('scripts')
      
    @endpush
