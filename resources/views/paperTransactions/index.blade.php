@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@push('style')
@endpush

@section('title')
    معاملات الورقية
@endsection

@section('content')
    <section>
        <div class="row">
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> معاملات الورقية </p>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row d-flex justify-content-between" dir="rtl">
                    <div class="form-group moftsh mt-4 mx-4 d-flex">
                        <p class="filter"> تصفية حسب :</p>
                        <div class="check-one d-flex pt-2">
                            <input type="checkbox" class="mx-2" name="all_date" checked id="all_date">
                            <label for=""> كل الأيام </label>
                        </div>
                        <div class="form-group moftsh select-box-2 mx-3 d-flex">
                            <input type="date" name="date" id="date" value="{{ $date ? $date : date('Y-m-d') }}">
                        </div>
                        <select id="group_id" name="group_id" class="form-control" placeholder="المجموعة">
                            <option value="-1" selected>كل المجموعات</option>
                            @foreach ($groups as $item)
                                <option value="{{ $item->id }}" {{ $group == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="form-group moftsh select-box-2 mx-3 d-flex">
                            <select id="group_team_id" name="group_team_id" class="form-control" placeholder="الفرق">
                                <option value="-1" selected> كل الدوريات </option>
                                @foreach ($groupTeams as $item)
                                    <option value="{{ $item->id }}" {{ $team == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group moftsh select-box-2 mx-3 d-flex">
                            <select id="inspectors" name="inspectors" class="form-control" placeholder="المفتش">
                                <option value="-1" selected> كل المفتشين </option>
                                @foreach ($inspectors as $item)
                                    <option value="{{ $item->id }}" {{ $inspector == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group moftsh mx-3 d-flex">
                            <button class="btn-all px-3" style="color: #212529; background-color: #f8f8f8;"
                                onclick="search()"> بحث </button>
                        </div>
                    </div>
                    <div class="form-group mt-4 mx-4 d-flex justify-content-end">
                        <button class="btn-all px-3" style="color: #FFFFFF; background-color: #274373;"
                            onclick="window.print()">
                            <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
                        </button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="bg-white">
                        @if (session()->has('message'))
                            <div class="alert alert-info">
                                {{ session('message') }}
                            </div>
                        @endif
                        <div>
                            <table id="users-table"
                                class="display table table-responsive-sm table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>رقم القيد</th>
                                        <th>رقم الأحوال</th>
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
        $(document).ready(function() {
            $('#group_id').on('change', function() {
                var group_id = $(this).val();
                $.ajax({
                    url: '/getGroups/' + group_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#group_team_id').empty();
                        $('#group_team_id').append(
                            '<option selected value="-1"> كل الدوريات </option>');
                        $.each(data, function(key, employee) {
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

            $('#group_team_id').on('change', function() {
                var group_team_id = $(this).val();
                var group_id = $('#group_id').val();
                $.ajax({
                    url: '/getInspector/' + group_team_id + '/' + group_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#inspectors').empty();
                        $('#inspectors').append('<option value="-1"> كل المفتشين </option>');
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
            });
        });

        function search() {
            var url = "{{ url('paperTransactions') }}";
            var dateItem = $('#date').val();
            var alldate = $('#all_date').val();
            var group = $('#group_id').val();
            var team = $('#group_team_id').val();
            var inspectors = $('#inspectors').val();
            var addurl = '';
            if (all_date == 0 && dateItem != '' && dateItem != null) {
                addurl += '?date=' + dateItem;
            }
            if (group) addurl += (addurl ? '&' : '?') + 'group=' + group;
            if (team) addurl += (addurl ? '&' : '?') + 'team=' + team;
            if (inspectors) addurl += (addurl ? '&' : '?') + 'inspector=' + inspectors;

            document.location = url + addurl;
        }

        $(document).ready(function() {
            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm';
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('paperTransactions/getAll') }}',
                columns: [{
                        data: 'civil_number',
                        name: 'civil_number'
                    },
                    {
                        data: 'registration_number',
                        name: 'registration_number'
                    },
                    {
                        data: 'action',
                        name: 'action',
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
                    sInfoFiltered: '(تم تصفية من _MAX_ اجمالى البيانات)',
                    sLengthMenu: 'اظهار _MENU_ عنصر لكل صفحة',
                    sZeroRecords: 'نأسف لا توجد نتيجة',
                    oPaginate: {
                        sFirst: '<i class="fa fa-fast-backward" aria-hidden="true"></i>',
                        sPrevious: '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                        sNext: '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
                        sLast: '<i class="fa fa-step-forward" aria-hidden="true"></i>'
                    }
                }
            });
        });
    </script>
@endpush
