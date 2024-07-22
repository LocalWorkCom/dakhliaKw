@extends('welcome')

@section('content')
<div class="container">
    <h1>Create Postman</h1>
    <form action="{{ route('postmans.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <select name="department_id" class="form-control">
                <option value="">اختر الادارة</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="name">الاسم</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="national_id">رقم الهوية</label>
            <input type="text" class="form-control" id="national_id" name="national_id" value="{{ old('national_id') }}">
            @error('national_id')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="phone1">الهاتف الاول</label>
            <input type="phone" class="form-control" id="phone1" name="phone1" value="{{ old('phone1') }}">
            @error('phone1')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="phone2">الهاتف الثانى</label>
            <input type="phone" class="form-control" id="phone2" name="phone2" value="{{ old('phone2') }}">
            @error('phone2')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection