@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Appointment details</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="appointment-item">
              <span>Patient</span>
              <div id="patient-name"></div>
            </div>
            <div class="appointment-item">
              <span>Type</span>
              <div id="appt-type"></div>
            </div>
            <div class="appointment-item">
              <span>Date</span>
              <div id="appt-date"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="wrapper">
      <div class="page-header">
        <h3>Appointments</h3>
      </div>

      <div class="flex-wrapper">
        <div class="table-menu">
          <h6>Clinics list</h6> 
          <ul>
            @foreach($tenants as $tenant)
            <li class="{{ $clinic->id ==  $tenant->id ? 'active' : ''}}"><a href="{{route('appointments.list', ['name' => $tenant->id])}}">{{$tenant->name}}</a></li>
            @endforeach
          </ul>
        </div>

        <div class="calendar-wrapper">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </main>
@endsection


@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },

    eventClick: function(info) {
      info.jsEvent.preventDefault();
      console.log(info.event)
      const event = info.event;
      const start = new Date(event.start);
      const end = new Date(event.end);
      console.log(start)
      let day = start.getDate();
      let month = start.toLocaleString('en-US', { month: 'short' });
      if (day < 10) {
          day = '0' + day;
      }
      let startTime = start.toLocaleString('en-US', { hour: "numeric", minute: "numeric", hour12: true});
      let endTime = end.toLocaleString('en-US', { hour: "numeric", minute: "numeric", hour12: true});
      const formattedTime = `${day} ${month} ${startTime} - ${endTime}`
      const userName = event.extendedProps.user
      $("#appointmentModal").find("#patient-name").html(userName);
      $("#appointmentModal").find("#appt-type").html(event.title);
      $("#appointmentModal").find("#appt-date").html(formattedTime);
      $("#appointmentModal").modal("toggle");
    },
    timeZone: 'local',
    events: [
      @foreach($appointments as $appointment)
      {
        title: "{{$appointment->visit_type->label()}}",
        start: "{{$appointment->start_date}}",
        end: "{{$appointment->end_date}}",
        extendedProps: {
          user: "{{$appointment->patient->user->first_name}} {{$appointment->patient->user->last_name}}"
        },
      },
      @endforeach
    ]
  });

  calendar.render();
});
</script>
@endpush