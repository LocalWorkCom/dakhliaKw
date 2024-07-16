@extends('layout.header')

@push('style')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

@endpush

@section('content')
<section style="direction: rtl;">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('Export.index') }}">الصادرات </a></li>

        <li class="breadcrumb-item active">تعديل الصادر</li>

    </ol>
    
    <div class="container-fluid" style="text-align: center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-block">
                    <form >

                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label for="name">العنوان</label>
                            <input type="text" class="form-control" name="name"  id="name" placeholder="العنوان">
                          </div>
                          <div class="form-group col-md-6">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control"  name="num" id="exportnum">
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="select-person-to">person_to </label>
                            <select id="select-person-to" name="person_to" class="form-control">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="select-created_by">created_by </label>
                            <select id="select-created_by" name="created_by" class="form-control">
                                @foreach ($users as $user )
                                <option value="{{ $user->id }}" >{{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="select-updated_by">updated_by </label>
                            <select id="select-updated_by" name="updated_by" class="form-control">
                                @foreach ($users as $user )
                                <option value="{{ $user->id }}" >{{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})</option>
                                @endforeach
                            </select>
                        </div>

                
                        <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="active">الحاله</label>
                                    <select id="active" name="active" >
                                        <option value="1" >مفعل</option>
                                        <option value="0">غير مفعل</option>
                                  
                                    </select>
                                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                                </div>
                                <div class="form-group col-md-6">
                                
                                    <label for="exampleFormControlFile1"> حمل الملف </label>
                                    <input type="file" class="form-control-file" id="exampleFormControlFile1">
                               
                                </div>
                               
                        </div>
                        <div class="form-row">
                             <!-- Button trigger modal -->
                             <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                                أضافه أداره خارجيه
                               </button>
                        </div>
                            <button class="btn btn-primary" type="submit">تعديل </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


  
  <!-- Modal -->
  <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle"> </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group col-md-12">
                <label for="newdepart">اسم الجهه الجديده </label>
               <input type="text" name="nameDepart" id="newdepart">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="name">العنوان</label>
                  <input type="text" class="form-control" name="name" id="name" placeholder="العنوان">
                </div>
                <div class="form-group col-md-6">
                  <label for="exportnum">رقم الصادر</label>
                  <input type="text" class="form-control"  name="num" id="exportnum">
                </div>
              </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
          <button type="button" class="btn btn-primary">أضف </button>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('javascripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>

    <script>
         $(document).ready(function () {
            $('#select-person-to').selectize({
                sortField: 'text',
                searchField: 'text',  // Ensure the search field is specified
                create: false  // Optional: Disable creating new items
            });
        });
        $(document).ready(function () {
            $('#select-created_by').selectize({
                sortField: 'text',
                searchField: 'text',  // Ensure the search field is specified
                create: false  // Optional: Disable creating new items
            });
        });
        $(document).ready(function () {
            $('#select-updated_by').selectize({
                sortField: 'text',
                searchField: 'text',  // Ensure the search field is specified
                create: false  // Optional: Disable creating new items
            });
            $('#myModal').on('shown.bs.modal', function () {
  $('#myInput').trigger('focus')
})
        });
    
    </script>
@endpush
