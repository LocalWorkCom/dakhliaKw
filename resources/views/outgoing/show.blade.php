@extends('layout.main')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

@endpush
@section('title')
التفاصيل
@endsection
@section('content')
<section style="direction: rtl;">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('Export.index') }}">الصادرات </a></li>

        <li class="breadcrumb-item active">تفاصيل الصادر</li>

    </ol>
    
    <div class="container-fluid" style="text-align: center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-block">
                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label for="name">العنوان</label>
                            <input type="text" class="form-control" name="name" value="{{ $data->name }}" id="name" placeholder="العنوان" disabled>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control"  name="num" id="exportnum" value="{{ $data->num }}" disabled>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" disabled>{{ $data->note }}</textarea>
                        </div>
                        <div class="form-group">
                          <label for="inputAddress2">person_to</label>
                          <select id="select-state"  disabled>
                            @foreach ($users as $user )
                            <option value="{{ $user->id }}" @if($user->id == $data->person_to) selected @endif>{{ $user->username }} (الرقم العسكرى : {{ $user->military_number }})</option>
                            @endforeach
                        
                          </select>
                        </div>
                        <div class="form-group">
                            <label for="inputAddress2">created_by</label>
                            <select id="select-state" disabled>
                              @foreach ($users as $user )
                              <option value="{{ $user->id }}" @if($user->id == $data->created_by) selected @endif>{{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})</option>
                              @endforeach
                          
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="inputAddress2">updated_by</label>
                            <select id="select-state" disabled>
                              @foreach ($users as $user )
                              <option value="{{ $user->id }}" @if($user->id == $data->updated_by) selected @endif>{{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})</option>
                              @endforeach
                          
                            </select>
                          </div>
                        
                        <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputAddress2">الحاله</label>
                                    <select id="select-state" disabled>
                                        <option value="1" @if($data->active == 1) selected @endif >مفعل</option>
                                        <option value="0" @if($data->active == 0) selected @endif>غير مفعل</option>
                                  
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    @unless ($is_file)
                                    <label for="exampleFormControlFile1"> file upload</label>
                                    <input type="file" class="form-control-file" id="exampleFormControlFile1">
                                  @endunless
                                </div>
                              </div>
                        </div>
                        
                </div>
            </div>
        </div>
    </div>

   
</section>
@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>

    <script>
          $(document).ready(function () {
                $('select').selectize({
                    sortField: 'text'
                });
            });
    </script>
@endpush
