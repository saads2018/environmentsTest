@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Patients</h3>

        <div class="page-header-btn">
          <a href="{{route('patient.create')}}">
            <img src="{{ Vite::asset('resources/images/dashboard/add.svg') }}" alt="Icon">
            <span>Add patient</span>
          </a>
        </div>
      </div>

      <div class="clinics-table patients-table">
        <div class="table-menu">
          <h6>Clinics list</h6>
          <ul>
            @foreach($tenants as $tenant)
            <li class="{{ $clinic->id ==  $tenant->id ? 'active' : ''}}"><a href="{{route('patient.list', ['name' => $tenant->id])}}">{{$tenant->name}}</a></li>
            @endforeach
          </ul>
        </div>

        <table>
            <thead>
              <tr>
                <th>Patient name</th>
                <th>Age</th>
                <th>Next appointment</th>
                <th>Questionnaire status</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
            @foreach($patients as $patient)
              <tr>
                <td>{{$patient->user->first_name}} {{$patient->user->last_name}}</td>
                <td>{{$patient->age}}</td>
                <td>{{$patient->nextAppt}}</td>
                <td>{{$patient->quizRequired ? 'Incomplete' : 'Complete'}}</td>
                <td><a href="{{route('edit-patient', ['clinicId' => $clinic->id, 'patientId' => $patient->id])}}">View</a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </main>
@endsection