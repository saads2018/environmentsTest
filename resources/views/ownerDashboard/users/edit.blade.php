@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Physician</h3>
      </div>

      <div class="physician-details">
        @hasrole('admin')
        <form action="{{route('users.update.admin', ['clinicId' => $clinic->id, 'id' => $physician->id])}}" method="post" enctype="multipart/form-data">
        @else
        <form action="{{route('users.update.owner', ['id' => $physician->id])}}" method="post" enctype="multipart/form-data">
        @endhasrole
            @method('PUT')
            @csrf
          <div class="physician-details-img">
            <img src="{{ Vite::asset('resources/images/dashboard/doctor.webp') }}" alt="Profile image">
          </div>

          <div class="physician-details-info">
            <div class="add-wrapper-item">
              <label>First Name</label>
              <input name="user[first_name]" type="text" value="{{$physician->user->first_name}}">
            </div>

            <div class="add-wrapper-item">
              <label>Last Name</label>
              <input name="user[last_name]" type="text" value="{{$physician->user->last_name}}">
            </div>

            <div class="add-wrapper-item">
              <label>Email</label>
              <input name="user[email]" type="text" value="{{$physician->user->email}}" readonly>
            </div>
            <div class="add-wrapper-item">
              <label>Phone no.</label>
              <input name="user[phone]" type="text" value="{{$physician->user->phone}}" />
            </div>

           

            <div class="add-wrapper-item">
              <label>Date of birth</label>
              <input name="profile[dob]" type="date" value="{{$physician->dob->format('Y-m-d')}}">
            </div>

            <div class="add-wrapper-item">
              <label>Gender
                <select name="profile[gender]" class="popup-content-item-input">
                    <option @selected($physician->gender == 'm') value="m">Male</option>
                    <option @selected($physician->gender == 'f') value="f">Female</option>
                </select>
              </label>
            </div> 

            <div class="add-wrapper-item">
                <label>Role
                  <select name="role" class="popup-content-item-input">
                  <option value="">Select role</option>
                  @foreach($roles as $role)
                    <option value="{{$role->id}}" @selected($physician->user->hasRole($role->id))>{{$role->name}}</option>
                  @endforeach
                  </select>
                </label>
              </div>
            

            <button class="add-btn">Save</button>
            <button type="button" class="add-btn" id="pwd-reset">Reset password</button>
            <button type="button" id="delete-user" class="add-btn" style="background: red;">Delete</button>
          </div>
        </form>

        @hasrole('admin')
        <form action="{{route('users.reset.admin', ['clinicId' => $clinic->id, 'physicianId' => $physician->id])}}" id="reset-form" method="post">
        @else
        <form action="{{route('users.reset.owner', ['physicianId' => $physician->id])}}" id="reset-form" method="post">
        @endhasrole
          @csrf
        </form>

        @hasrole('admin')
        <form action="{{route('users.delete.admin', ['clinicId' => $clinic->id, 'physicianId' => $physician->id])}}" id="delete-form" method="post">
        @else
        <form action="{{route('users.delete.owner', ['physicianId' => $physician->id])}}" id="delete-form" method="post">
        @endhasrole
          @csrf
        </form>
      </div>
  </main>
@endsection

@push('script')
<script>
    $(document).ready(function () {
        $("#pwd-reset").on('click', function(e) {
            e.stopPropagation();
            $('#reset-form').trigger('submit')

        });

        $("#delete-user").on('click', function(e) {
            e.stopPropagation();
            let prompt = confirm("This will delete this user and all related data. Are you sure to proceed?");
            if(prompt) {
                $('#delete-form').trigger('submit')
            }

        });

    });
</script>
@endpush