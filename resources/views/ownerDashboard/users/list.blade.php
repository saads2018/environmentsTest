@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Users</h3>

        <div class="page-header-btn">
          <a href="{{route('users.new.owner')}}">
            <img src="{{ Vite::asset('resources/images/dashboard/add.svg') }}" alt="Icon">
            <span>Add user</span>
          </a>
        </div>
      </div>

      <div class="clinics-table patients-table">
        <table>
            <thead>
              <tr>
                <th>User name</th>
                <th>Email</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
              @foreach($physicians as $physician)
              <tr>
                <td>{{$physician->user->first_name}} {{$physician->user->last_name}}</td>
                <td>{{$physician->user->email}}</td>
                <td><a href="{{route('users.edit.owner', ['physicianId' => $physician->id])}}">View</a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </main>
@endsection