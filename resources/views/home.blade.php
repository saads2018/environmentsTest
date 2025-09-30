@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ __('Users') }}

                    <table class="table table-striped">
                      <thead class="thead-dark">
                        <tr>
                          <th scope="col">UUID</th>
                          <th scope="col">Name</th>
                          <th scope="col">Roles</th>
                          <th scope="col">Info</th>
                          <th scope="col">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($users as $user)
                        <tr>
                          <th scope="row">{{ __($user->id) }}</th>
                          <td>{{ __($user->name) }}</td>
                          <td>{{ __($user->roles->pluck('name')->implode(',')) }}</td>
                          <td>{{ $user->tenant ? $user->tenant->tenancy_db_name : '' }}</td>
                          <td>@role('admin')<a type="button" class="btn btn-danger" href="{{ route('delete-user', ['id' => $user->id]) }}">{{ __('Delete') }}</a>@endrole</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @role('admin')
                     {{ __('Add tenant') }}
                    <form method="POST" action="{{ route('create-user') }}">
                    @csrf
                      <div class="mb-3">
                        <label for="name" class="form-label">Clinic name</label>
                        <input type="text" name="clinic-name" class="form-control" id="name">
                      </div>
                      <div class="mb-3">
                        <label for="subdomain" class="form-label">Clinic subdomain</label>
                        <input type="text" name="clinic-subdomain" class="form-control" id="subdomain">
                      </div>
                      <div class="mb-3">
                        <label for="fullname" class="form-label">Full name</label>
                        <input type="text" name="name" class="form-control" id="fullname">
                      </div>
                      <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" id="email">
                      </div>
                      <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password">
                      </div>
                      <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
