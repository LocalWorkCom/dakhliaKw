@extends('layout.main')

@section('content')

<div class="row col-11" dir="rtl">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="{{ route('home') }}">الرئيسيه</a></li>
        <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">الادارات </a></li>
        <li class="breadcrumb-item active" aria-current="page"> <a href="#">تفاصيل  الادارة</a></li>
      </ol>
    </nav>
  </div>
  <div class="row ">
    <div class="container welcome col-11">
      <p> الـــــــــادارات </p>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="container  col-11 mt-3 p-0 ">
      <div class="row " dir="rtl">
        <div class="form-group mt-4  mx-2 col-12 d-flex ">
          ********buttons******
        </div>
      </div>
      <div class="form-row mx-2 ">
        <table class="table table-bordered" dir="rtl">
          <tbody>
            <tr>
              <th scope="row" style="background: #f5f6fa;">اسم الادارة</th>
              <td style="background: #f5f6fa;">{{ $department->name }}</td>
            </tr>
            <tr>
              <th scope="row">المدير</th>
              <td>{{ $department->manager ? $department->manager->id : 'N/A' }}</td>
            </tr>
            <tr>
              <th scope="row">  مساعد المدير</th>
              <td> {{ $department->managerAssistant ? $department->managerAssistant->id : 'N/A' }}</td>
            </tr>
            @if($department->children->count() > 0)
<tr>
    @foreach($department->children as $child)
      <th scope="row"> القسم الفرعي </th>
              <td>{{ $child->name }} </td>
              @endforeach
            </tr>
            @else
            <td>لا يوجد </td>
            @endif

          </tbody>
        </table>
      </div>
    </div>
  </div>
  <br> <br> <br> <br>


<!-- <div class="container">
    <h1>Department Details</h1>

    <div class="card">
        <div class="card-header">
            Department #{{ $department->id }}
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $department->name }}</h5>
            <p class="card-text"><strong>Manager:</strong> {{ $department->manager ? $department->manager->id : 'N/A' }}</p>
            <p class="card-text"><strong>Manager Assistant:</strong> {{ $department->managerAssistant ? $department->managerAssistant->id : 'N/A' }}</p>
            <p class="card-text"><strong>Description:</strong> {{ $department->description }}</p>
            <p><strong>Parent Department:</strong> {{ $department->parent ? $department->parent->name : 'None' }}</p>

        </div>
    </div>
    <h2>Child Departments</h2>
    @if($department->children->count() > 0)
        <ul>
            @foreach($department->children as $child)
                <li>{{ $child->name }}</li>
            @endforeach
        </ul>
    @else
        <p>No child departments.</p>
    @endif
    <a href="{{ route('departments.index') }}" class="btn btn-primary mt-3">Back to Departments</a>
</div> -->
@endsection
