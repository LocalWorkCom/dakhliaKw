@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@push('style')
@endpush
@section('title')
    سجل المخالفات
@endsection
@section('content')
    <section>
        <div class="row">

            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p>الأحصائيات </p>

                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">

                <div class="row d-flex justify-content-between " dir="rtl">
                    <div class="form-group moftsh mt-4  mx-4  d-flex">
                        <p class="filter "> تصفية حسب :</p>

                        <form class="edit-grade-form" action="{{ route('statistic.search') }}" method="post">
                            @csrf
                            <div class="check-one d-flex pt-2">
                                <input type="checkbox" class="mx-2" name="all_date" checked id="all_date">
                                <label for=""> كل الايام </label>
                            </div>
                            <div class="form-group moftsh select-box-2  mx-3  d-flex">
                                <h4 style="    line-height: 1.8;"> التاريخ : </h4>
                                <input type="date" name="date" id="date">

                            </div>
                            <div class="form-group moftsh select-box-2 mx-3  d-flex">
                                <h4 style=" line-height: 1.8;"> النقطه : </h4>
                                <select id="points" name="points"
                                    class="form-control custom-select custom-select-lg mb-3 select2 ">
                                    <option value="-1" selected> كل النقاط
                                    </option>
                                    @foreach ($points as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group moftsh select-box-2 mx-3  d-flex">
                                <h4 style=" line-height: 1.8;"> المخالفه : </h4>
                                <select id="violation" name="violation"
                                    class="form-control custom-select custom-select-lg mb-3 select2 ">

                                    <option value="-1" selected> كل المخالفات
                                    </option>
                                    @foreach ($violations as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group moftsh select-box-2 mx-3  d-flex">
                                <h4 style=" line-height: 1.8;"> المفتش : </h4>
                                <select id="inspectors" name="inspectors"
                                    class="form-control custom-select custom-select-lg mb-3 select2 " placeholder="المفتش">

                                    <option value="-1" selected> كل المفتشين
                                    </option>
                                    @foreach ($inspectors as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group moftsh  mx-3  d-flex">
                                <button class="btn-all px-3 " style="color: #212529; background-color: #f8f8f8;"
                                    onclick="search()">
                                    بحث
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
                <section style="direction: rtl;">
                    <div class="col-lg-12">
                        <div class="bg-white ">
                            @if (session()->has('message'))
                                <div class="alert alert-info">
                                    {{ session('message') }}
                                </div>
                            @endif
                            <div class="container  col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11">
                                @foreach ($violationData as $violation)
                                    
                                @endforeach
                                <table class="table table-bordered" dir="rtl">
                                    <tbody>
                                        <tr>
                                            <th>التاريخ :</th>
                                            <td>{{$violation->created_at ?  $violation->created_at->format('d-m-Y') :'' }}</td>
                                        </tr>
                                        <tr>
                                            <th>النقطه :</th>
                                            <td>{{ $violation->point_id ? $violation->point->name : ''}}</td>
                                        </tr>

                                        <tr>
                                            <th scope="row">نوع المخالفه </th>
                                        </tr>
                                        <tr>
                                            <th scope="row">المفتش</th>
                                        </tr>

                                    </tbody>
                                </table>
                              @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </section>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $('.select2').select2({
            dir: "rtl"
        });
    </script>
    {{-- <script>
        function search() {
            var url = "";
            var dateItem = $('#date').val();
            var alldate = $('#all_date').val();
            var group = $('#points').val();
            var team = $('#violation').val();
            var inspectors = $('#inspectors').val();
            var addurl = '';
            if (all_date == 0) {
                if (dateItem != '' || dateItem != null) {
                    if (addurl == '') addurl += '?';
                    else addurl += '&';
                    addurl += 'date=' + dateItem;
                }
            }
            if (group) {
                if (addurl == '') addurl += '?';
                else addurl += '&';
                addurl += 'group=' + group;
            }
            if (team) {
                if (addurl == '') addurl += '?';
                else addurl += '&';
                addurl += 'team=' + team;
            }
            if (inspectors) {
                if (addurl == '') addurl += '?';
                else addurl += '&';
                addurl += 'inspector=' + inspectors;
            }
            document.location = url + addurl;
        }
    </script>
--}}
    <script>
        $(document).ready(function() {
            $('#date').on('change', function() {
                const selectedDate = $(this).val();
                //  $('#selectedDate').text('Selected Date: ' + selectedDate);
                $('#all_date').prop('checked', false).val('0');
                console.log('Date changed to: ', selectedDate);
            });
        });
    </script>
@endpush
