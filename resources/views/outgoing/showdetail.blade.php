@extends('layout.main')

@push('style')

@endpush

@section('content')
<div class="row" style="direction: rtl;">
<nav style="--bs-breadcrumb-divider: '>';" class="breadcrumb-nav" >
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('Export.index') }}">الصادرات </a></li>
        <li class="breadcrumb-item active" aria-current="page">تفاصيل الصادر</li>
    </ol>
    </nav>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> الصـــــــــادرات</p>
    </div>
</div>

<section style="direction: rtl;">
<div class="row">
<div class="container c col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11">
    <table class="table table-bordered ">
  <tbody>
    <tr style="background-color:#f5f6fa;">
      <th scope="row">الراسل</th>
      <td>{{$data->person_to ? $data->personTo->name :'لا يوجد موظف للصادر'}}</td>
    </tr>
    <tr>
      <th scope="row">العنوان</th>
      <td>{{ $data->name ? $data->name :'لا يوجد عنوان للصادر' }}</td>
    </tr>
    <tr>
      <th scope="row">اسم الاداره</th>
      <td>{{ $data->department_id ? $data->department_External->name : 'لا يوجد قسم خارجى'}}</td>
    </tr>
    <tr>
      <th scope="row">رقم الصادر</th>
      <td>{{ $data->num ? $data->num :'لا يوجد رقم للصادر' }}</td>
    </tr>
    <tr>
      <th scope="row"> الحالة</th>
      <td>{{ $data->active ? 'مفعل' : 'غير مفعل' }}</td>
    </tr>
    <tr>
      <th scope="row">الملاحظات </th>
      <td>{{ $data->note ? $data->note :'لايوجد ملاحظات للصادر' }}</td>
    </tr>
  </tbody>
  <tfoot>
        <tr>
          <th>الملفات</th>
            <td>
                @if(!empty($is_file))
                        @foreach ($is_file as $file )
                        <embed src="{{ asset($file->file_name) }}" width="100px" height="80px" />
                        <a href="{{ route('downlaodfile', $file->id) }}" class="btn btn-info btn-sm" ><i class="fa fa-download"></i></a>
                        @endforeach
                @else  
                لايوجد ملفات للصادر
                @endif
            </td>
          
        </tr>
    </tfoot>

</table>
      
    </div>

   
</section>
@endsection

@push('scripts')

@endpush
