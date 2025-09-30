@extends('layouts.dashboard')

@section('content')
<main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Statistics</h3>
      </div>

      <div class="clinics-table patients-table">
        <div class="table-menu">
          <h6>Clinics list</h6>
          <ul>
          @foreach($tenants as $tenant)
            <li class="{{ $clinic->id ==  $tenant->id ? 'active' : ''}}"><a href="{{route('stats.list', ['name' => $tenant->id])}}">{{$tenant->name}}</a></li>
            @endforeach
          </ul>
        </div>

        <div class="stats-wrapper">
          <div class="stats-wrapper-top">
            <table>
              <thead>
                <tr>
                  <th>Patient name</th>
                  <th>Appointments per month</th>
                </tr>
              </thead>
              <tbody>
                @foreach($patients as $patient)
                <tr>
                  <td>{{$patient->user->first_name}} {{$patient->user->last_name}}</td>
                  <td>{{$patient->apptCount}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>

            <div class="stats-wrapper-total">
              <h6>Total Appointments</h6>
              <span>{{ count($appointments) }}</span>
            </div>
          </div>

          <div class="stats-wrapper-visit stats-wrapper-visit-second">
            <div class="stats-wrapper-visit-second-item box_blue">
              <div>Initial NaviWell Visit</div>
              <span>{{ count($appointments->byApptType(App\Enums\AppointmentVisitType::INITIAL)) }}</span>
            </div>
            <div class="stats-wrapper-visit-second-item box_green">
              <div>Wellness Coach Visit</div>
              <span>{{ count($appointments->byApptType(App\Enums\AppointmentVisitType::WELLNESS)) }}</span>
            </div>
            <div class="stats-wrapper-visit-second-item box_pink">
              <div>Dietitian Visit</div>
              <span>{{ count($appointments->byApptType(App\Enums\AppointmentVisitType::DIETITIAN)) }}</span>
            </div>
            <div class="stats-wrapper-visit-second-item box_yellow">
              <div>Follow-Up Visit</div>
              <span>{{ count($appointments->byApptType(App\Enums\AppointmentVisitType::FOLLOWUP)) }}</span>
            </div>
          </div>

          <div class="stats-wrapper-item">
            <h6>Patients</h6>
            <canvas id="bar-chart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </main>
  @endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById("bar-chart"), {
    type: 'bar',
    data: {
      labels: ["May", "June", "July", "August", "September", "October", "November", "December"],
      datasets: [
        {
          label: "New",
          backgroundColor: ["#2AC15D"],
          data: [1, 2, 3, 4, 5, 6, 7, 8]
        },
        {
          label: "Active",
          backgroundColor: ["#498CD0"],
          data: [12, 13, 14, 15, 16, 17, 18, 19]
        },
        {
          label: "Inactive",
          backgroundColor: ["#F4D85B"],
          data: [9, 10, 11, 20, 21, 22, 23, 24]
        }
      ]
    },
    options: {
      legend: { display: false },
      title: {
        display: true,
      },
      plugins: {
        legend: {
          labels: {
            font: {
              size: 16
            }
          }
        }
      }
    }
});
</script>

@endpush