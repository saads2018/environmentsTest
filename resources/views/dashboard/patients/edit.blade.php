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
              <span>{{$patient->currentHealthData?->height ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>Weight</span>
              <span>{{$patient->currentHealthData?->weight ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>BMI</span>
              <span>{{$patient->currentHealthData?->bmi ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>Body Fat</span>
              <span>{{$patient->currentHealthData?->bodyfat ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>BP</span>
              <span>{{$patient->currentHealthData?->bp ?? 'Undefined'}}</span>
            </div>
            <div class="patient-details-healthdata-item">
              <span>Resting HR</span>
              <span>{{$patient->currentHealthData?->resting_hr ?? 'Undefined'}}</span>
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
                <span>Completed with {{$quiz->score}} score</span>
              </li>
              @endforeach
            </ul>
          </div>

          @if(count($questionaires) > 0)
          <div class="patient-details-quizzes">
            <h4>Questionnaires</h4>
            
            <div class="result-table">
              <table>
                <thead>
                  <tr>
                    <th>Date taken</th>
                    <th>Report</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($questionaires as $q)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($q['created_at'])->format('d/m/Y'); }}</td>
                    <td>
                      <a href="{{$q['link']}}">
                        <button class="add-btn">Download</button>
                      </a>
                      </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          @endif

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

          <div class="chart-wrapper">
            <div>
              <canvas id="dataChart"></canvas>
            </div>
            <div>
              <canvas id="bpChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
    <form action="{{route('patients.reset.admin', ['clinicId' => $clinic->id, 'patientId' => $patient->id])}}" id="reset-form" method="post">
      @csrf
    </form>
  </main>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const dataChart = document.getElementById('dataChart');
  const bpChart = document.getElementById('bpChart');
  new Chart(dataChart, {
    data: {
      labels: {!! json_encode( array_map(function($key, $value) { return \Carbon\Carbon::parse($value['created_at'])->format('d/m/Y'); }, array_keys($chartData), $chartData) ) !!},
      datasets: [{
        type: 'line',
        label: "Weight",
        id: 'weight',
        data: {!! json_encode( array_map(function($key, $value) { return ['x' => \Carbon\Carbon::parse($value['created_at'])->format('d/m/Y'), 'y' => $value['weight']]; }, array_keys($chartData), $chartData) ) !!},
        borderColor: "#4766FF",
        borderWidth: 2,
        fill: false,
    },
    {
        type: 'line',
        label: "Resting Heart Rate",
        id: 'resting_hr',
        data: {!! json_encode( array_map(function($key, $value) { return ['x' => \Carbon\Carbon::parse($value['created_at'])->format('d/m/Y'), 'y' => $value['resting_hr']]; }, array_keys($chartData), $chartData) ) !!},
        borderColor: "#FF4D4D",
        borderWidth: 2,
        fill: false,
    },
    {
        type: 'line',
        label: "Body Fat",
        id: 'bodyfat',
        data: {!! json_encode( array_map(function($key, $value) { return ['x' => \Carbon\Carbon::parse($value['created_at'])->format('d/m/Y'), 'y' => $value['bodyfat']]; }, array_keys($chartData), $chartData) )  !!},
        borderColor: "#FFB54F",
        borderWidth: 2,
        fill: false,
    },
    ],
    },
    options: {
      responsive: true,
      lineTension: 1,
    },
  });
  new Chart(bpChart, {
    data: {
      datasets: [
    {
        type: 'scatter',
        showLine: false,
        label: "Blood Presure",
        id: 'bp',
        data: {!! json_encode( array_map(function($key, $value) { return isset($value['bp']) ? ['x' => explode('/', $value['bp'])[0], 'y' => explode('/', $value['bp'])[1]] : null; }, array_keys($chartData), $chartData) ) !!},
        borderColor: "#73D44D",
        borderWidth: 5,
        fill: false,
    },
    ],
    },
    options: {
      responsive: true,
      lineTension: 1,
    },
  });

  $("#pwd-reset").on('click', function(e) {
        e.stopPropagation();
        $('#reset-form').trigger('submit')

    });
</script>

@endpush