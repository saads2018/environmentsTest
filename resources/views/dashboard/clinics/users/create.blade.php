@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Add user for {{$clinic->name}} facility</h3>
            </div>

            <div class="add-wrapper">
                <form action="{{route('users.create.admin', ['clinicId' => $clinic->id])}}" method="post" enctype="multipart/form-data">
                    @method('POST')
                    @csrf
                    <div class="add-wrapper-item">
                        <label>First Name</label>
                        <input name="user[first_name]" type="text" placeholder="" />
                    </div>

                    <div class="add-wrapper-item">
                        <label>Last Name</label>
                        <input name="user[last_name]" type="text" placeholder="" />
                    </div>

                    <div class="add-wrapper-item">
                        <label>Email
                            <input name="user[email]" type="email" class="popup-content-item-input" />
                        </label>
                    </div>

                    <div class="add-wrapper-item">
                        <label>Phone no.</label>
                        <input name="user[phone]" type="text" placeholder="" />
                    </div>

                    <div class="add-wrapper-item">
                        <label>Date of birth
                            <input name="profile[dob]" type="date" class="popup-content-item-input" />
                        </label>
                    </div>

                    <div class="add-wrapper-item">
                        <label>Gender
                        <select name="profile[gender]" class="popup-content-item-input">
                            <option value="m">Male</option>
                            <option value="f">Female</option>
                        </select>
                        </label>
                    </div>

                    <div class="add-wrapper-item">
                        <label>Role
                        <select name="role" class="popup-content-item-input">
                            <option value="">Select role</option>
                            @foreach($roles as $role)
                            <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                        </label>
                    </div>

                    <button class="add-btn">Save</button>
                </form>
            </div>
        </div>
    </main>
@endsection