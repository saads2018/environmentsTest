@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Patients</h3>
      </div>

      <div class="clinics-table patients-table">
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
                <td><a href="{{route('patient.edit.owner', ['patientId' => $patient->id])}}">Edit</a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </main>
@endsection