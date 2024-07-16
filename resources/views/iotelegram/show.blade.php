@extends('layout.header')

@section('title')
    عرض
@endsection
@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('iotelegrams.list') }}" class="btn btn-primary mt-3">رجوع</a>
        </div>

        <div class="card">
            <div class="card-header">الواردات</div>
            <div class="card-body">

                <div class="mb-3">
                    <label for="date">التاريخ:</label>
                    <input type="date" id="date" name="date" class="form-control">
                </div>
                <div class="row" style="justify-content: space-evenly;">
                    <div class="mb-3">
                        <input type="checkbox" id="extern" name="type">
                        <label for="checkbox">داخلي</label>
                    </div>
                    <div class="mb-3">
                        <input type="checkbox" id="intern" name="type">
                        <label for="checkbox">خارجي</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="departments">الجهة المرسلة:</label>
                    <select id="departments" name="departments" class="form-control">
                        <option value="">اختر الجهة</option>
                        @foreach ($departments as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                        data-bs-target="#exampleModal">
                        <i class="fa fa-plus"></i>
                    </button>
                    <label for="representive">اسم المندوب الجهة المرسلة :</label>
                    <select id="representive" name="representive" class="form-control">
                        <option value="">اختر المندوب</option>
                        @foreach ($representives as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="recieved_by">الموظف المستلم:</label>
                    <select id="recieved_by" name="recieved_by" class="form-control">
                        <option value="">اختر الموظف</option>
                        @foreach ($recieves as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="files_num"> عدد الكتب:</label>
                    <br>
                    <select id="files_num" name="files_num" class="form-control">
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

            </div>
        </div>
    </div>
@endsection
