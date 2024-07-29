@extends('layout.main')

@push('style')

@endpush
@section('title')
    أضافه
@endsection
@section('content')
<div class="row col-11" dir="rtl">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
            <li class="breadcrumb-item"><a href="{{ route('government.all') }}">المحافظات </a></li>
            <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة محافظه </a></li>
        </ol>
    </nav>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> المحـــافظات </p>
    </div>
</div>
<br>
<section style="direction: rtl;">
<div class="row">
<div class="container  col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11">
    <table class="table table-bordered ">
  <tbody>
    <tr style="background-color:#f5f6fa;">
      <th scope="row">الاسم</th>
      <td>{{$data->name }}</td>
    </tr>

  </tbody>

</table>

    </div>


</section>
@endsection

@push('scripts')

@endpush
