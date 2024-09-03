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
                    <p> </p>

                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0 ">

                <div class="row d-flex justify-content-between " dir="rtl">
                    <div class="form-group moftsh mt-4  mx-4  d-flex">
                        <p class="filter "> تصفية حسب :</p>
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
                    </div>

                </div>

                <div class="col-lg-12">
                    <div class="bg-white ">
                        @if (session()->has('message'))
                            <div class="alert alert-info">
                                {{ session('message') }}
                            </div>
                        @endif
                        <div>
                            <table id="users-table"
                                class="display table table-responsive-sm  table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>التاريخ </th>
                                        <th>النقاط</th>
                                        <th>المخالفات</th>
                                        <th>المفتشون</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
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
        $('#search-btn').click(function() {
    $('#users-table').DataTable().ajax.reload();
});
        $(document).ready(function() {


            // Initialize DataTable
            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url('statistics/search') }}', // Initial URL
                    data: function(d) {
                        // Add custom filter parameters
                        d.date = $('#date').val();
                        d.points = $('#points').val();
                        d.violation = $('#violation').val();
                        d.inspector = $('#inspectors').val();
                    }
                },
                columns: [{
                        data: 'Date',
                        sWidth: '50px',
                        name: 'Date'
                    },
                    {
                        data: 'points',
                        sWidth: '50px',
                        name: 'points'
                    },
                    {
                        data: 'Violations',
                        sWidth: '50px',
                        name: 'Violations'
                    },
                    {
                        data: 'inspectors',
                        name: 'inspectors',
                        sWidth: '50px',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                oLanguage: {
                    sSearch: "",
                    sSearchPlaceholder: "بحث",
                    sInfo: 'اظهار صفحة _PAGE_ من _PAGES_',
                    sInfoEmpty: 'لا توجد بيانات متاحه',
                    sInfoFiltered: '(تم تصفية  من _MAX_ اجمالى البيانات)',
                    sLengthMenu: 'اظهار _MENU_ عنصر لكل صفحة',
                    sZeroRecords: 'نأسف لا توجد نتيجة',
                    oPaginate: {
                        sFirst: '<i class="fa fa-fast-backward" aria-hidden="true"></i>',
                        sPrevious: '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                        sNext: '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
                        sLast: '<i class="fa fa-step-forward" aria-hidden="true"></i>'
                    }
                },
                pagingType: "full_numbers",
                fnDrawCallback: function(oSettings) {
                    var page = this.api().page.info().pages;
                    if (page == 1) {
                        $('.dataTables_paginate').css('visibility',
                        'hidden'); // to hide pagination if only one page
                    }
                }
            });

            // Search button click event
            $('#search-btn').click(function() {
                table.ajax.reload(); // Reload the table data with new filters
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#group_id').on('change', function() {
                var group_id = $(this).val();


                //if (group_id!=-1) {
                $.ajax({
                    url: '/getGroups/' + group_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#group_team_id').empty();
                        $('#group_team_id').append(
                            '<option selected value="-1"> كل الدوريات </option>');
                        $.each(data, function(key, employee) {
                            console.log(employee);
                            $('#group_team_id').append('<option value="' + employee.id +
                                '">' + employee.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        console.log('XHR:', xhr.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#group_team_id').on('change', function() {
                var group_team_id = $(this).val();
                var group_id = $('#group_id').val();
                console.log(group_team_id);

                $.ajax({
                    url: '/getInspector/' + group_team_id + '/' + group_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#inspectors').empty();
                        $('#inspectors').append('<option value="-1"> كل المفتشين   </option>');
                        $.each(data, function(key, employee) {
                            $('#inspectors').append('<option value="' + employee.id +
                                '">' + employee.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        console.log('XHR:', xhr.responseText);
                    }
                });
                /*} else {
                    $('#inspectors').empty();
                }*/
            });
        });
    </script>
    <script>
        function search() {
            var url = "";
            var dateItem = $('#date').val();
            var alldate = $('#all_date').val();
            var group = $('#group_id').val();
            var team = $('#group_team_id').val();
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
