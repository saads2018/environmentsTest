@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Diagnostic results</h3>

        <div class="page-header-btn">
          <a  href="{{route('result.create.form', ['clinicId' => $clinic->id])}}">
            <img src="{{ Vite::asset('resources/images/dashboard/add.svg') }}" alt="Icon">
            <span>Add diagnostic result</span>
          </a>
        </div>
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
            <li class="{{ $clinic->id ==  $tenant->id ? 'active' : ''}}"><a href="{{route('result.list', ['name' => $tenant->id])}}">{{$tenant->name}}</a></li>
            @endforeach
          </ul>
        </div>

        <div class="grid">
        @foreach($results as $result)
            <a  href="{{route('edit-result', ['clinicId' => $clinic->id, 'resultId' => $result->id])}}" class="grid-item">
              <div class="grid-item-content">
                <h6>{{$result->name}}</h6>
                <span>{{$result->patient->user->first_name}} {{$result->patient->user->last_name}}</span>

                <div class="grid-item-content-icon">
                  <img src="{{ Vite::asset('resources/images/dashboard/date.svg') }}" alt="Icon">
                  <span>{{$result->date->format('m/d/Y')}}</span>
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