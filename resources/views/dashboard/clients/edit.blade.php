@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>User</h3>
      </div>

      <div class="physician-details">
        <form action="{{route('clients.update', ['userId' => $user->id])}}" method="post" enctype="multipart/form-data">
            @method('PUT')
            @csrf

          <div class="physician-details-info">
            <div class="add-wrapper-item">
              <label>Name</label>
              <input name="name" type="text" value="{{$user->name}}">
              @error('name')
                    <div class="error-message">{{$message}}</div>
              @enderror
            </div>

            <div class="add-wrapper-item">
              <label>Email</label>
              <input name="email" type="text" value="{{$user->email}}" readonly>
              @error('email')
                    <div class="error-message">{{$message}}</div>
              @enderror
            </div>

            @if(auth()->user()->id != $user->id)
            <div class="add-wrapper-item">
              <label>Manager (has access to corporate data)</label>
              
              <input name="is-admin" type="checkbox" value="1" @checked($user->hasRole('admin'))>
              @error('is-admin')
                    <div class="error-message">{{$message}}</div>
              @enderror
            </div>
            @endif

            <button class="add-btn">Save</button>
            <button type="button" class="add-btn" id="pwd-reset">Reset password</button>
            <button type="button" id="delete-user" class="add-btn" style="background: red;">Delete</button>
          </div>
        </form>

        <form action="{{route('clients.reset', ['userId' => $user->id])}}" id="reset-form" method="post">
          @csrf
        </form>

        <form action="{{route('clients.delete', ['userId' => $user->id])}}" id="delete-form" method="post">
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