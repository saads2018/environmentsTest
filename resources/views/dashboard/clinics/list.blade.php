@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Clinics</h3>

        <div class="page-header-btn">
          <a href="{{route('clinic.create-form')}}">
            <img src="{{ Vite::asset('resources/images/dashboard/add.svg') }}" alt="Icon">
            <span>Add clinic</span>
          </a>
        </div>
      </div>

      <div class="clinics-table">
        <table>
            <thead>
              <tr>
                <th>Clinic name</th>
                <th>User</th>
                <th>Email</th>
                <th>Address</th>
                <th>Physicians</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
            @foreach($tenants as $tenant)
              <tr>
                <td class="clinic-logo">
                  <img src="{{ $tenant->logoUrl ?: Vite::asset('resources/images/dashboard/naviwell-logo.png') }}" alt="Naviwell logo">
                  <span>{{ $tenant->name }}</span>
                </td>
                <td>{{ $tenant->user->name }}</td>
                <td>
                  <a href="mailto:{{ $tenant->user->email }}">{{ $tenant->user->email }}</a>
                </td>
                <td>{{ $tenant->address }}</td>
                <td>{{ $tenant->userCount }}</td>
                <td>
                  <a href="{{route('edit-clinic', ['id' => $tenant->id])}}">Edit</a>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </main>
@endsection