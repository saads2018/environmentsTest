@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Add a new patient</h3>
            </div>

            <div class="add-wrapper">
                <form action="{{route('patient.create.post')}}" method="post">
                    @method('POST')
                    @csrf

                    <label for="">Clinic</label>
                    <select class="add-multiselect" name="clinic" id="clinic_select" style="width: 50%">
                    @foreach($clinics as $clinic)
                        <option value="{{$clinic->id}}">{{$clinic->name}}</option>
                    @endforeach
                    </select>
                    @error('clinic')
                        <div class="error-message">{{$message}}</div>
                    @enderror


                    <div class="add-wrapper-item">
                        <label for="firstName">First Name</label>
                        <input type="text" name="user[first_name]" class="@error('user.first_name') error @enderror" value="{{ old('user.first_name') }}" required>
                        @error('user.first_name')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                        <label for="lastName">Last name</label>
                        <input type="text" name="user[last_name]" class="@error('user.last_name') error @enderror" value="{{ old('user.last_name') }}" required>
                        @error('user.last_name')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                        <label for="email">Email</label>
                        <input type="email" name="user[email]" class="@error('user.email') error @enderror" value="{{ old('user.email') }}" required>
                        @error('user.email')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                        <label for="number">Phone number</label>
                        <input type="text" name="user[phone]" class="@error('user.phone') error @enderror" value="{{ old('user.phone') }}" required>
                        @error('user.phone')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                      <label>Date of birth</label>
                      <input name="profile[dob]" type="date" class="@error('profile.dob') error @enderror" value="{{ old('profile.dob') }}" required/>
                      @error('profile.dob')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                      <label>Gender</label>
                      <select name="profile[gender]" class="@error('profile.gender') error @enderror" value="{{ old('profile.gender') }}" required>
                        <option value="m">Male</option>
                        <option value="f">Female</option>
                      </select>
                      @error('profile.gender')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item row">
                        <label for="height">Height</label>
                        <input type="number" placeholder="ft" name="health-data[height_ft]" class="@error('health-data.height_ft') error @enderror" value="{{ old('health-data.height_ft') }}" required>
                        <input type="number" placeholder="in" name="health-data[height_in]" class="@error('health-data.height_in') error @enderror" value="{{ old('health-data.height_in') }}" required>
                        @error('health-data.height_ft', 'health-data.height_in')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                        <label for="weight">Weight</label>
                        <input type="number" name="health-data[weight]" class="@error('health-data.weight') error @enderror" value="{{ old('health-data.weight') }}" required>
                        @error('health-data.weight')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>

                    <button class="add-btn">Save</button>
                </Form>
            </div>
        </div>
    </main>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

$('.add-multiselect').select2();

</script>
@endpush