@extends('layout.main')

@push('style')
@endpush
@section('title')
    التفاصيل
@endsection
@section('content')
<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sectors.index') }}">القطاعات</a></li>
            <li class="breadcrumb-item active" aria-current="page"> <a href=""> تفاصيل القطاع</a></li>
        </ol>
    </nav>
</div>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> القطــــاعات </p>
    </div>
</div>
<br>

    <section style="direction: rtl;">
        <div class="row">
            <div class="container c col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11 pt-5 pb-4 px-3">
                <table class="table table-bordered ">
                    <tbody>
                        <tr style="background-color:#f5f6fa;">
                            <th scope="row"> أسم القطاع </th>
                            <td>{{ $data->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">المحافظات الخاصه بهذا القطاع</th>
                            <td>
                                @foreach (getgovernments() as $government)
                                @if(isset($checkedGovernments[$government->id])) {{ $government->name }} @endif
                            @endforeach
                        </td>
                            {{-- <td>{{ $data->name ? $data->name : 'لا يوجد عنوان للصادر' }}</td> --}}
                        </tr>
                        
                    </tbody>
                    <tfoot>
                        
                    </tfoot>

                </table>

            </div>

            
    </section>
@endsection

@push('scripts')
   
@endpush
