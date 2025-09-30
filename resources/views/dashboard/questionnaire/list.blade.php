@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Questionnaire results</h3>
      </div>

      <div class="search-wrapper">
        <form method="get">
          <label>
            <input placeholder="Search" type="search" autocomplete="off">
            <SearchIcon />
          </label>
        </form>
      </div>

      <div class="flex-wrapper">
        <div class="table-menu">
          <h6>Clinics list</h6> 
          <ul>
            @foreach($tenants as $tenant)
            <li class="{{ $clinic->id ==  $tenant->id ? 'active' : ''}}"><a href="{{route('questionnaire.list', ['name' => $tenant->id])}}">{{$tenant->name}}</a></li>
            @endforeach
          </ul>
        </div>

        <div class="grid">
        @foreach($quizzes as $quiz)
            <a href="{{route('questionnaire.view', ['clinicId' => $clinic->id, 'quizId' => $quiz->id])}}" class="grid-item">
              <div class="grid-item-content">
                <h6>{{$quiz->patient->user->first_name}} {{$quiz->patient->user->last_name}}</h6>
                <span>Questionnaire name</span>

                <div class="grid-item-content-icon">
                  <img src="{{ Vite::asset('resources/images/dashboard/date.svg') }}" alt="Icon">
                  <span>{{$quiz->date}}</span>
                </div>
              </div>
              <div class="grid-item-btn">View</div>
            </a>
          @endforeach
        </div>
      </div>
    </div>
  </main>
@endsection