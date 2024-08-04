@extends('layout.main')

@push('style')
@endpush
@section('title')
    التفاصيل
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('points.index') }}"> نقاط الوزاره </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a> تفاصيل</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> نقاط الوزاره </p>
        </div>
    </div>
    <br>

    <section style="direction: rtl;">
        <div class="row">
            <div class="container c col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11 pt-5 pb-4 px-3">
                <table class="table table-bordered ">
                    <tbody>
                        <tr style="background-color:#f5f6fa;">
                            <th scope="row"> أسم نقطه الوزاره </th>
                            <td>{{ $data->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row"> القطاع الخاص بها</th>
                            <td>
                                {{-- @foreach (getgovernments() as $government)
                                    @if (isset($checkedGovernments[$government->id])) --}}
                                        {{ $data->sector->name }}
                                    {{-- @endif
                                @endforeach --}}
                            </td>
                            {{-- <td>{{ $data->name ? $data->name : 'لا يوجد عنوان للصادر' }}</td> --}}
                        </tr>
                        <tr>
                            <th scope="row">المحافظه الخاصه بها</th>
                            <td>
                                {{ $data->government->name }}
                            </td>
                            {{-- <td>{{ $data->name ? $data->name : 'لا يوجد عنوان للصادر' }}</td> --}}
                        </tr>


                        <tr>
                            <th scope="row">المنطقه الخاصه بها</th>
                            <td>
                                {{ $data->region->name }}

                            </td>
                            {{-- <td>{{ $data->name ? $data->name : 'لا يوجد عنوان للصادر' }}</td> --}}
                        </tr>

                        <tr>
                            <th scope="row">موعد بدايه العمل</th>
                            <td>
                              {{ $data->formatted_from }}
                            </td>
                            {{-- <td>{{ $data->name ? $data->name : 'لا يوجد عنوان للصادر' }}</td> --}}
                        </tr>
                        <tr>
                            <th scope="row">موعد نهايه العمل</th>
                            <td>
                                {{ $data->formatted_to }}

                            </td>
                            {{-- <td>{{ $data->name ? $data->name : 'لا يوجد عنوان للصادر' }}</td> --}}
                        </tr>
                        <tr>
                            <th scope="row"> رابط جوجل ماب </th>
                            <td>
                               {{$data->google_map ? $data->google_map : 'لا يوجد رابط جوجل ماب'}}
                            </td>
                            {{-- <td>{{ $data->name ? $data->name : 'لا يوجد عنوان للصادر' }}</td> --}}
                        </tr>

                        <tr>
                            <th scope="row">الملاحظات</th>
                            <td>
                               {{ $data->note  ? $data->note : 'لا يوجد ملاحظات' }}
                            </td>
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
