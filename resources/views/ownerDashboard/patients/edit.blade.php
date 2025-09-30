@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Patient details</h3>
      </div>

      <div class="patient-details">
        <div class="left">
          <div class="patient-details-healthdata">
            <div class="patient-details-info">
              <div>{{$patient->user->first_name}} {{$patient->user->last_name}}</div>
              <span>{{$patient->age}} y.o.</span>
            </div>

            <p>{{$patient->user->email}}</p>
            <button type="button" class="add-btn" id="pwd-reset">Reset password</button>

            <div class="patient-details-healthdata-item">
              <span>Height</span>
              <span>{{$patient->healthData?->height ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>Weight</span>
              <span>{{$patient->healthData?->weight ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>BMI</span>
              <span>{{$patient->healthData?->bmi ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>Body Fat</span>
              <span>{{$patient->healthData?->bodyfat ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>BP</span>
              <span>{{$patient->healthData?->bp ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>Resting HR</span>
              <span>{{$patient->healthData?->resting_hr ?? 'Undefined'}}</span>
            </div>
          </div>
        </div>

        <div class="right">
          <div class="patient-details-quizzes">
            <h4>Quizzes</h4>

            <ul>
              @foreach ($patient->completedQuizzes as $quiz )
              <li class="complete">
                <div>{{$quiz->title}}</div>
                <span>Completed with {{$quiz->pivot->score}} score</span>
              </li>
              @endforeach
            </ul>
          </div>

          <div class="patient-details-results">
            <h4>Diagnostic results</h4>

            <ul>
            @foreach ($patient->labResults as $result )
              <li class="complete">
                <div>{{$result->name}}</div>
                <span>{{$result->date->format("m.d.Y")}}</span>
              </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    </div>

    <form action="{{route('patients.reset.owner', ['patientId' => $patient->id])}}" id="reset-form" method="post">
      @csrf
    </form>
  </main>
@endsection

@push('script')
<script>
    $(document).ready(function () {
        $("#pwd-reset").on('click', function(e) {
            e.stopPropagation();
            $('#reset-form').trigger('submit')

        });

    });
</script>
@endpush