@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Users</h3>

        <div class="page-header-btn">
          <a href="{{route('clients.new')}}">
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
              @foreach($users as $user)
              <tr>
                <td>{{$user->name}} <i style="color:red;">{{$user->hasRole('admin') ? "(Admin)" : "" }}</i></td>
                <td>{{$user->email}}</td>
                <td><a href="{{route('clients.edit', ['userId' => $user->id])}}">View</a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </main>
@endsection