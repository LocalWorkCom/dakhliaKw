@extends('layout.main')

<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8"
    src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8"
    src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
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
                    <p> سجل المخالفـــات</p>

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
                            <input type="checkbox" class="mx-2"
                                value="{{ $allDate }}" name="all_date"
                                @if ($allDate == 1) checked @endif
                                id="all_date">
                            <label for=""> كل الايام </label>
                        </div>
                        <div class="form-group moftsh select-box-2  mx-3  d-flex">
                            <!-- <h4 style="    line-height: 1.8;"> التاريخ :</h4> -->
                            <input type="date" name="date" id="date"
                                value="{{ $date ? $date : date('Y-m-d') }}">

                        </div>
                        <div class="form-group moftsh select-box-2 mx-3  d-flex">
                            <!-- <h4 style=" line-height: 1.8;"> المجموعة :</h4> -->
                            <select id="group_id" name="group_id"
                                class="form-control select2" placeholder="المجموعة">

                                <option value="-1" selected> كل المجموعات
                                </option>
                                @foreach ($groups as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $group == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group moftsh select-box-2  mx-3  d-flex">
                            <!-- <h4 style=" line-height: 1.8;"> الفريق :</h4> -->
                            <select id="group_team_id" name="group_team_id"
                                class="form-control select2" placeholder="الفرق">

                                <option value="-1" selected> كل الدوريات
                                </option>
                                @foreach ($groupTeams as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $team == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group moftsh select-box-2 mx-3  d-flex">
                            <!-- <h4 style=" line-height: 1.8;"> المفتش :</h4> -->
                            <select id="inspectors" name="inspectors"
                                class="form-control select2" placeholder="المفتش">

                                <option value="-1" selected> كل المفتشين
                                </option>
                                @foreach ($inspectors as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $inspector == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group moftsh  mx-3  d-flex">
                            <button class="btn-all px-3 "
                                style="color: #212529; background-color: #f8f8f8;"
                                onclick="search()">
                                بحث
                            </button>
                        </div>
                    </div>
                    <div class="form-group mt-4 mx-4  d-flex justify-content-end ">
                        <button class="btn-all px-3 "
                            style="color: #FFFFFF; background-color: #274373;"
                            onclick="window.print()">
                            <img src="{{ asset('frontend/images/print.svg') }}"
                                alt=""> طباعة
                        </button>
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
                                        <th>النوع </th>
                                        <th>الاسم</th>
                                        <th>نوع المخالفه</th>

                                        <th style="width:150px;">العمليات</th>
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
    <script>
        $('.select2').select2({
            dir: "rtl"
        });
        $('#group_id').change(function() {
            var group_id = $(this).val();


            //if (group_id!=-1) {
            $.ajax({
                url: '/getGroups/' + group_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#group_team_id').empty();
                    $('#group_team_id').append(
                        '<option selected value="-1"> كل الدوريات </option>'
                    );
                    $.each(data, function(key,
                        employee) {
                        console.log(
                            employee);
                        $('#group_team_id')
                            .append(
                                '<option value="' +
                                employee
                                .id + '">' +
                                employee
                                .name +
                                '</option>'
                            );
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr
                        .responseText);
                }
            });
            /*} /* else {
                $('#group_team_id').empty();
            } */
        });

        /**Team change */
        $('#group_team_id').change(function() {
            var group_team_id = $(this).val();
            var group_id = $('#group_id').val();
            console.log(group_team_id);


            // if (group_id!=-1) {
            $.ajax({
                url: '/getInspector/' + group_team_id +
                    '/' + group_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#inspectors').empty();
                    $('#inspectors').append(
                        '<option value="-1"> كل المفتشين   </option>'
                    );
                    $.each(data, function(key,
                        employee) {
                        // console.log(employee);
                        $('#inspectors')
                            .append(
                                '<option value="' +
                                employee
                                .id + '">' +
                                employee
                                .name +
                                '</option>'
                            );
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr
                        .responseText);
                }
            });
            /*} else {
                $('#inspectors').empty();
            }*/
        });
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
    <script>
        function search() {
            var url = "{{ url('viollation') }}";
            //debugger;
            var dateItem = $('#date').val();
            var all_date = $('#all_date').val();
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
            console.log('addurl =>' + addurl)

            console.log('Date =>' + dateItem)
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

        $(document).ready(function() {


            $.fn.dataTable.ext.classes.sPageButton =
                'btn-pagination btn-sm'; // Change Pagination Button Class
            @php
                $Dataurl = url('violation/getAll');
                $url = '';
                if (isset($date) && $date != '-1') {
                    if ($url == '') {
                        $url .= '?';
                    } else {
                        $url .= '&';
                    }
                    $url .= 'date=' . $date;
                }
                if (isset($group) && $group != '-1') {
                    if ($url == '') {
                        $url .= '?';
                    } else {
                        $url .= '&';
                    }
                    $url .= 'group=' . $group;
                }
                if (isset($team) && $team != '-1') {
                    if ($url == '') {
                        $url .= '?';
                    } else {
                        $url .= '&';
                    }
                    $url .= 'team=' . $team;
                }
                if (isset($inspector) && $inspector != '-1') {
                    if ($url == '') {
                        $url .= '?';
                    } else {
                        $url .= '&';
                    }
                    $url .= 'inspector=' . $inspector;
                }
                $Dataurl .= $url;
                //dd($Dataurl);
            @endphp
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: '{{ $Dataurl }}', // Correct URL concatenation
                columns: [{
                        data: 'Type',
                        sWidth: '50px',
                        name: 'Type'
                    },
                    {
                        data: 'name',
                        sWidth: '50px',
                        name: 'name'
                    },
                    {
                        data: 'ViolationType',
                        sWidth: '50px',
                        name: 'ViolationType'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        sWidth: '100px',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                "oLanguage": {
                    "sSearch": "",
                    "sSearchPlaceholder": "بحث",
                    "sInfo": 'اظهار صفحة _PAGE_ من _PAGES_',
                    "sInfoEmpty": 'لا توجد بيانات متاحه',
                    "sInfoFiltered": '(تم تصفية  من _MAX_ اجمالى البيانات)',
                    "sLengthMenu": 'اظهار _MENU_ عنصر لكل صفحة',
                    "sZeroRecords": 'نأسف لا توجد نتيجة',
                    "oPaginate": {
                        "sFirst": '<i class="fa fa-fast-backward" aria-hidden="true"></i>', // This is the link to the first page
                        "sPrevious": '<i class="fa fa-chevron-left" aria-hidden="true"></i>', // This is the link to the previous page
                        "sNext": '<i class="fa fa-chevron-right" aria-hidden="true"></i>', // This is the link to the next page
                        "sLast": '<i class="fa fa-step-forward" aria-hidden="true"></i>' // This is the link to the last page
                    }


                },
                layout: {
                    bottomEnd: {
                        paging: {
                            firstLast: false
                        }
                    }
                },
                "pagingType": "full_numbers",
                "fnDrawCallback": function(oSettings) {
                    var api = this.api();
                    var pageInfo = api.page.info();

                    // Check if the total number of records is less than or equal to the number of entries per page
                    if (pageInfo.recordsTotal <=
                        10
                    ) { // Adjust this number based on your page length
                        $('.dataTables_paginate').css(
                            'visibility', 'hidden'
                        ); // Hide pagination
                    } else {
                        $('.dataTables_paginate').css(
                            'visibility', 'visible'
                        ); // Show pagination
                    }
                }

            });


        });

        // window.onload = function() {
        //     const urlParams = new URLSearchParams(window.location.search);
        //     const group = urlParams.get('group');
        //     const team = urlParams.get('team');
        //     const inspector = urlParams.get('inspector');

        //     // Check if any query parameters are set
        //     if (group !== null || team !== null || inspector !== null) {
        //         // Redirect to the base URL without query parameters
        //         window.location.href = "https://developement.testdomain100.online/viollation";
        //     }
        // };
    </script>
@endpush
