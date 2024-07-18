@extends('layout.header')

@section('title', 'الواردات')

@section('content')
<<<<<<< HEAD
<div class="row ">
    <div class="container welcome col-11">
        <p> الــــــواردات</p>
    </div>
</div>

<div class="container  col-11 mt-3 p-0 ">
        <div class="row justify-content-end">
            <div class="col-auto">
            <button class="btn-all mt-3">
                    <a  href="{{ route('iotelegrams.add') }}" style="color:#0D992C;">إضافة جديد <img
                            src="{{ asset('frontend/images/addnew.svg')}}" alt=""></a>
                </button>
                
=======
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('iotelegrams.add') }}" class="btn btn-primary mt-3">إضافة جديد</a>
        </div>
        @include('inc.flash')

        <div class="card">
            <div class="card-header">الواردات</div>

            <div class="card-body">

                {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
>>>>>>> 5be7510574168ba3b84256676f4babf4ce099cc6
            </div>
        </div>
          
         
                {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
           
        </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    {!! $dataTable->scripts() !!}
    <script>
        function confirmArchive(event, ele) {
            event.preventDefault();

            Swal.fire({
                title: 'تأكيد الأرشفة',
                text: "هل أنت متأكد أنك تريد نقل هذا العنصر إلى الأرشيف؟",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، أرشفه!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = $(ele).attr('href');
                }
            });
        }
    </script>
@endpush
