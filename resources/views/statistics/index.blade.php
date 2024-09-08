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
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">

                <div class="row d-flex justify-content-between " dir="rtl">
                    <div class="form-group moftsh mt-4  mx-4  d-flex">
                        <p class="filter "> تصفية حسب :</p>

                        <div class="check-one d-flex pt-2">
                            <input type="checkbox" class="mx-2" name="all_date" 
                                   id="all_date" {{ request('date') == '-1' ? 'checked' : '' }}>
                            <label for="all_date"> كل الايام </label>
                        </div>
                        
                        <div class="form-group moftsh select-box-2  mx-3  d-flex">
                            <h4 style="line-height: 1.8;"> التاريخ : </h4>
                            <input type="date" name="date" id="date" value="{{ request('date') !== '-1' ? request('date') : '' }}">
                        </div>
                        
                        <div class="form-group moftsh select-box-2 mx-3  d-flex">
                            <h4 style="line-height: 1.8;"> النقطه : </h4>
                            <select id="points" name="points" class="form-control custom-select custom-select-lg mb-3 select2">
                                <option value="-1" {{ request('point') == '-1' ? 'selected' : '' }}> كل النقاط</option>
                                @foreach ($points as $item)
                                    <option value="{{ $item->id }}" {{ request('point') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group moftsh select-box-2 mx-3  d-flex">
                            <h4 style="line-height: 1.8;"> المخالفه : </h4>
                            <select id="violation" name="violation" class="form-control custom-select custom-select-lg mb-3 select2">
                                <option value="-1" {{ request('violation') == '-1' ? 'selected' : '' }}> كل المخالفات</option>
                                @foreach ($violations as $item)
                                    <option value="{{ $item->id }}" {{ request('violation') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group moftsh select-box-2 mx-3  d-flex">
                            <h4 style="line-height: 1.8;"> المفتش : </h4>
                            <select id="inspectors" name="inspectors" class="form-control custom-select custom-select-lg mb-3 select2">
                                <option value="-1" {{ request('inspector') == '-1' ? 'selected' : '' }}> كل المفتشين</option>
                                @foreach ($inspectors as $item)
                                    <option value="{{ $item->id }}" {{ request('inspector') == $item->id ? 'selected' : '' }}>
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
                                @if ($results && !empty($results))
                                <table class="table table-bordered" dir="rtl">
                                    <tbody>
                                        <tr>
                                            <th>التاريخ :</th>
                                            <td>{{ $results['date'] ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>عدد المخالفة :</th>
                                            <td>{{ $results['violationCount'] ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>عدد النقاط :</th>
                                            <td>{{ $results['pointCount'] ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>عدد المفتشين :</th>
                                            <td>{{ $results['inspectorCount'] ?? '' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <p>No results found</p>
                            @endif
                            

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
        // Initialize select2 plugin for dropdowns
        $('.select2').select2({
            dir: "rtl"
        });
    
        // Search function to redirect with selected filters
        function search() {
            var url = "{{ url('/statistics/search') }}";
            var dateItem = $('#date').val();
            var allDateChecked = $('#all_date').is(':checked');
            var point = $('#points').val();
            var type = $('#violation').val();
            var inspectors = $('#inspectors').val();
            var addurl = '';
    
            // Handle all dates checkbox
            if (allDateChecked) {
                if (addurl == '') addurl += '?';
                else addurl += '&';
                addurl += 'date=-1';  // This sets the "All Date" option
            } else if (dateItem) {
                if (addurl == '') addurl += '?';
                else addurl += '&';
                addurl += 'date=' + dateItem;
            }
    
            // Inspector filter
            if (inspectors) {
                if (addurl == '') addurl += '?';
                else addurl += '&';
                addurl += 'inspector=' + inspectors;
            }
    
            // Point filter
            if (point) {
                if (addurl == '') addurl += '?';
                else addurl += '&';
                addurl += 'point=' + point;
            }
    
            // Violation filter
            if (type) {
                if (addurl == '') addurl += '?';
                else addurl += '&';
                addurl += 'violation=' + type;
            }
    
            // Redirect with filters
            document.location = url + addurl;
        }
    
        // Date input handling
        $(document).ready(function() {
            $('#date').on('change', function() {
                $('#all_date').prop('checked', false);  // Uncheck "All Date" when a specific date is selected
                console.log('Date changed to: ', $(this).val());
            });
    
            // Check/uncheck "All Date" checkbox
            $('#all_date').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#date').val('');  // Clear date when "All Date" is checked
                }
            });
        });
    </script>
    
@endpush
