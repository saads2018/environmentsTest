@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>User</h3>
      </div>

      <div class="physician-details">
        <form action="{{route('clients.create')}}" method="post" >
            @csrf

          <div class="physician-details-info">
            <div class="add-wrapper-item">
              <label>Name</label>
              <input name="name" type="text" value="{{ old('name') }}"  class="@error('name') error @enderror" >
              @error('name')
                    <div class="error-message">{{$message}}</div>
              @enderror
            </div>

            <div class="add-wrapper-item">
              <label>Email</label>
              <input name="email" type="text" value="{{ old('email') }}"  class="@error('email') error @enderror" >
              @error('email')
                    <div class="error-message">{{$message}}</div>
              @enderror
            </div>

            <div class="add-wrapper-item">
              <label>Manager (has access to corporate data)</label>
              <input name="is-admin" type="checkbox" value="1">
            </div>

            <button class="add-btn">Save</button>
          </div>
        </form>
      </div>
  </main>
@endsection