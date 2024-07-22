@extends('layout.main')
@section('content')

<section>
    <div class="row col-11">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="#" >الرئيسيه</a></li>
            <li class="breadcrumb-item"><a href="#">المستخدمين  </a></li>
            <li class="breadcrumb-item active" aria-current="page"> <a href="#"> اضافه مستخدم</a></li>
          </ol>
        </nav>
  </div>
     
    <div class="container-fluid p-5"  >
        <div class="row">
            <div class="col-lg-7 offset-2">
                <div class="bg-white">
                    @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {{-- {{ dd($flag) }} --}}
                    <div class="row ">
                        <div class="container welcome col-11">
                            <p> المستخـــــــــــدمين </p>
                        </div>
                    </div>

                    <div class="p-5">
                        <form  action="{{ route('user.store') }}" method="post" class="text-right">
                            @csrf

                            <input type="hidden" name="type" value="{{ $flag }}">
                            <div class="row" >
                                <div class="container  col-11 mt-3 p-0 ">
                                   
                                    <div class="form-row mx-2">
                                        <div class="form-group col-md-6">
                                            <label for="military_number">رقم العسكرى</label>
                                            <input type="text" id="military_number" name="military_number" class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="phone">رقم المحمول</label>
                                            <input type="text" id="phone" name="phone" class="form-control" required>
                                        </div>
                    
                                    </div>
                                    <div class="form-row mx-2">
                                        <div class="form-group col-md-6">
                                            <label for="filenum">رقم الملف</label>
                                                    <input type="text" id="filenum" name="file_number" class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="department">الادارة</label>
                                                    <select class="custom-select custom-select-lg mb-3" name="department"  id="department">
                                                        <option selected>Open this select menu</option>
                                                        @foreach ($alldepartment as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                      </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row mx-2">
                                        <div class="form-group col-md-12">
                                            <label for="rule_id">الادوار</label>
                                                    <select class="custom-select custom-select-lg mb-3" name="rule"  id="rule_id">
                                                        <option selected>Open this select menu</option>
                                                        @foreach ($rule as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                      </select>
                                        </div>
                                    </div>
                    
                                    <div class="container col-12 ">
                                        <div class="form-row mt-4 mb-5">
                                            <button type="submit" class="btn-blue">حفظ</button>
                                        </div>
                                       </div>
                                    <br>
                                </div>
                    
                    
                    
                            </div>




                            <div class="mb-3 ">
                                 <label for="nameus"> الاسم</label>
                                 <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                             <div class="mb-3">
                                 <label for="phone">رقم المحمول</label>
                                 <input type="text" id="phone" name="phone" class="form-control" required>
                             </div>
                            <div class="mb-3">
                                <label for="military_number">رقم العسكرى</label>
                                <input type="text" id="military_number" name="military_number" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="filenum">رقم الملف</label>
                                <input type="text" id="filenum" name="file_number" class="form-control" required>
                            </div>
                            @if ($flag == "0")
                                <div class="mb-3">
                                    <label for="password">الباسورد</label>
                                    <input type="text" id="password" name="password" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="rule_id">الادوار</label>
                                    <select class="custom-select custom-select-lg mb-3" name="rule"  id="rule_id">
                                        <option selected>Open this select menu</option>
                                        @foreach ($rule as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                          
                             <div class="form-group mb-3">
                                <label for="department">الادارة</label>
                                <select class="custom-select custom-select-lg mb-3" name="department"  id="department">
                                    <option selected>Open this select menu</option>
                                    @foreach ($alldepartment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                  </select>
                            </div>
                            {{-- @if ($flag == "1")
                                
                            @endif --}}
                           
         
                            <!-- Save button -->
                            <div class="text-end text-center">
                                <button type="submit" class="btn btn-primary btn-lg">حفظ</button>
                            </div>
                        </form>
                            
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>

   
</section>

    
@endsection
