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
                        <input type="checkbox" class="mx-2" name="all_date" id="all_date" >
                        <label for=""> كل الايام </label>
                    </div>
                    <div class="form-group moftsh select-box-2  mx-3  d-flex">
                            <!-- <h4 style="    line-height: 1.8;"> التاريخ :</h4> -->
                          <input type="date" name="date" id="date" value="{{($date)?$date:date('Y-m-d')}}">
                       
                        </div>
                        <div class="form-group moftsh select-box-2 mx-3  d-flex">
                        <!-- <h4 style=" line-height: 1.8;"> المجموعة :</h4> -->
                            <select id="group_id" name="group_id" class="form-control" placeholder="المجموعة">
                             
                                <option value="-1"
                                selected disabled  > المجموعة
                                    </option>
                                @foreach ($groups as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $group == $item->id ? 'selected' : '' }}> {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group moftsh select-box-2 mx-3  d-flex">
                             <!-- <h4 style=" line-height: 1.8;"> المفتش :</h4> -->
                            <select id="inspectors" name="inspectors" class="form-control" placeholder="المفتش">
                                 
                                    <option value="-1" selected disabled> المفتش
                                    </option>
                                    @foreach ($inspectors as $item)
                                        <option value="{{ $item->id }}"
                                            {{$inspector == $item->id ? 'selected' : '' }}> {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                        </div>
                        <div class="form-group moftsh select-box-2  mx-3  d-flex">
                        <!-- <h4 style=" line-height: 1.8;"> الفريق :</h4> -->
                        <select id="group_team_id" name="group_team_id" class="form-control" placeholder="الفرق">
                             
                                <option value="-1" selected disabled> الفريق
                                    </option>
                                @foreach ($groupTeams as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $team == $item->id ? 'selected' : '' }}> {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group moftsh  mx-3  d-flex">
                        <button class="btn-all px-3 " style="color: #FFFFFF;background-color: #a5a5a5;"
                        onclick="search()">
                    بحث  
                    </button>
                        </div>
                </div>
                <div class="form-group mt-4 mx-4  d-flex justify-content-end ">
                    <button class="btn-all px-3 " style="color: #FFFFFF; background-color: #274373;"
                        onclick="window.print()">
                        <img src="{{ asset('frontend/images/print.svg') }}" alt=""> طباعة
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
 $(document).ready(function() {
    $('#group_id').on('change', function() {
        var group_id = $(this).val();
       

        if (group_id!=-1) {
            $.ajax({
                url: '/getGroups/' + group_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#group_team_id').empty();
                    $('#group_team_id').append('<option selected value="-1"> الكل </option>');
                    $.each(data, function(key, employee) {               
                        console.log(employee);   
                        $('#group_team_id').append('<option value="' + employee.id + '">' + employee.name + '</option>');
                    });                 
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr.responseText);
                }
            });
        } else {
            $('#group_team_id').empty();
        }
    });
});

$(document).ready(function() {
    $('#group_team_id').on('change', function() {
        var group_team_id = $(this).val();
        var group_id = $('#group_id').val();
        console.log(group_team_id);
       

        if (group_id!=-1) {
            $.ajax({
                url: '/getInspector/' + group_team_id +'/'+  group_id ,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#inspectors').empty();
                    $('#inspectors').append('<option value='-1'> الكل   </option>');
                    $.each(data, function(key, employee) {               
                        // console.log(employee);   
                        $('#inspectors').append('<option value="' + employee.id + '">' + employee.name + '</option>');
                    });                 
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr.responseText);
                }
            });
        } else {
            $('#inspectors').empty();
        }
    });
});
</script>
<script>

function search()
{
    var url="{{url('viollation')}}";
    var dateItem=$('#date').val();
    var group=$('#group_id').val();
    var team=$('#group_team_id').val();
    var inspectors=$('#inspectors').val();
    var addurl='';
    if(dateItem!='' || dateItem!=null)
    {
        if(addurl=='')addurl+='?';else addurl+='&';
        addurl+='date='+dateItem;
    }
    if(group)
    {
        if(addurl=='')addurl+='?';else addurl+='&';
        addurl+='group='+group;
    }
    if(team)
    {
        if(addurl=='')addurl+='?';else addurl+='&';
        addurl+='team='+team;
    }
    if(inspectors)
    {
        if(addurl=='')addurl+='?';else addurl+='&';
        addurl+='inspector='+inspectors;
    }
    document.location=url+addurl;
}

$(document).ready(function() {


    $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class
    @php
                                        $Dataurl= url('violation/getAll') ;
                                        $url="";
                                      if(isset($date) && $date!='-1')
                                      {
                                        if($url=='')$url.='?'; else $url.='&';
                                        $url.='date='.$date;
                                      }
                                      if(isset($group) && $group!='-1')
                                      {
                                        if($url=='')$url.='?'; else $url.='&';
                                        $url.='group='.$group;
                                      }
                                      if(isset($team) && $team!='-1')
                                      {
                                        if($url=='')$url.='?'; else $url.='&';
                                        $url.='team='.$team;
                                      }
                                      if(isset($inspector) && $inspector!='-1')
                                      {
                                        if($url=='')$url.='?'; else $url.='&';
                                        $url.='inspector='.$inspector;
                                      }
                                      $Dataurl.=$url;
                                        //dd($Dataurl);
                                                                        
                                        @endphp    
    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ $Dataurl}}', // Correct URL concatenation
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
                                         console.log('Page '+this.api().page.info().pages)
                                        var page=this.api().page.info().pages;
                                        console.log($('#users-table tr').length);
                                        if (page ==1) {
                                         //   $('.dataTables_paginate').hide();//css('visiblity','hidden');
                                            $('.dataTables_paginate').css('visibility', 'hidden');  // to hide

                                        }
                                    }

    });


});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    var openSecondModalBtn = document.getElementById('openSecondModalBtn');
    var firstModalBody = document.getElementById('firstModalBody');
    var secondModalBody = document.getElementById('secondModalBody');

    // Add click event listener
    openSecondModalBtn.addEventListener('click', function() {
        // Hide the first modal body
        firstModalBody.classList.add('d-none');

        // Show the second modal body
        secondModalBody.classList.remove('d-none');
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectBox = document.getElementById('select-box');
    const options = document.getElementById('options');
    const searchInput = document.getElementById('search-input');
    const selectedValuesContainer = document.getElementById('selected-values');
    const optionCheckboxes = document.querySelectorAll('.option input[type="checkbox"]');
    selectBox.addEventListener('click', function() {
        options.style.display = options.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.select-wrapper')) {
            options.style.display = 'none';
        }
    });
    optionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const selectedOptions = Array.from(optionCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.nextElementSibling.textContent);
            selectedValuesContainer.innerHTML = selectedOptions.join(', ');
        });
    });
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        optionCheckboxes.forEach(checkbox => {
            const optionLabel = checkbox.nextElementSibling.textContent.toLowerCase();
            checkbox.parentElement.style.display = optionLabel.includes(searchTerm) ?
                'block' : 'none';
        });
    });
});
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('all_date');
        const dateInput = document.getElementById('date');
        
        function updateDateInputVisibility() {
            if (checkbox.checked) {
                dateInput.style.display = 'none'; 
            } else {
                dateInput.style.display = 'block'; 
            }
        }
        updateDateInputVisibility();
        checkbox.addEventListener('change', updateDateInputVisibility);
    });
</script>

@endpush