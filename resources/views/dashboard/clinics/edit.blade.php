@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Edit clinic</h3>
            </div>

            <div class="add-wrapper full-form">
                <form action="{{route('clinic.update', ['id' => $clinic->id])}}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="add-wrapper-item photo">
                        <input type="file" class="form-control" id="img-picker" name="logo" style="display:none" accept="image/*" />
                        <div class="add-wrapper-item-btn upload-photo">
                            <img id="picked-result" src="{{ $clinic->logoUrl ?: Vite::asset('resources/images/dashboard/upload.svg') }}" alt="Clinic logo"> 
                            <button type="button">Upload logo</button>
                        </div>

                        <div class="uploaded-img">
                            <img src="{{ Vite::asset('resources/images/dashboard/naviwell-logo.png') }}" alt="Naviwell logo">
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="add-wrapper-item">
                            <label for="email">Owner's Email</label>
                            <input type="text" name="email" value="{{$clinic->user->email}}" readonly>
                        </div>
                        <div class="add-wrapper-item">
                            <label for="clinicName">Clinic name</label>
                            <input type="text" name="name" value="{{$clinic->name}}" class="@error('name') error @enderror">
                            @error('name')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="add-wrapper-item">
                            <label for="email">Owner's Full Name</label>
                            <input type="text" name="email" value="{{$clinic->user->name}}" readonly>
                        </div>
                        <div class="add-wrapper-item">
                            <label for="address">Address</label>
                            <input type="text" name="address" value="{{$clinic->address}}" class="@error('address') error @enderror">  
                            @error('address')
                                <div class="error-message">{{$message}}</div>
                            @enderror 
                        </div>
                        <div class="add-wrapper-item">
                            <div class="subdomain">
                                <label for="url">Subdomain</label>
                                <input type="text" name="subdomain" value="{{$clinic->id}}" readonly>
                            </div>
                        </div>
                        
                        <div class="add-wrapper-item">
                            <label for="description">Description</label>
                            <textarea name="description" class="@error('description') error @enderror">{{$clinic->description}}</textarea>
                            @error('description')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>
                    </div>

                    <button class="add-btn">Save</button>
                    <button type="button" class="add-btn" id="pwd-reset">Reset admin password</button>
                    <a href="{{route('users.new.admin', ['clinicId' => $clinic->id])}}"><button type="button" class="add-btn">Create new user</button></a>
                    <button type="button" id="clinic-delete" class="add-btn" style="background: red;">Delete</button>

                </Form>
                <form action="{{route('pwd.reset.admin', ['clinicId' => $clinic->id])}}" id="reset-form" method="post">
                        @csrf
                </form>
                <form action="{{route('clinic.delete', ['clinicId' => $clinic->id])}}" id="delete-form" method="post">
                        @csrf
                </form>
                <br>
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
                            <td>{{$physician->user->first_name}} {{$physician->user->last_name}} <i style="color:red;">{{($physician->user->email == $clinic->user->email) ? "(Admin)" : "" }}</i> </td>
                            <td>{{$physician->user->email}}</td>
                            <td><a href="{{route('users.edit.admin', ['clinicId' => $clinic->id, 'physicianId' => $physician->id])}}">Edit</a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </main>
@endsection

@push('script')
<script>
    $(document).ready(function () {
        $(".upload-photo").on('click', function(e) {
            e.stopPropagation();
            $('#img-picker').click()
        });

        $("#img-picker").change(function(){
            readURL(this);
        });
    });

    $("#pwd-reset").on('click', function(e) {
        e.stopPropagation();
        $('#reset-form').trigger('submit')

    });

    $("#clinic-delete").on('click', function(e) {
        e.stopPropagation();
        let prompt = confirm("This will delete the facility with all the data! Are you sure to proceed?");
        if(prompt) {
            $('#delete-form').trigger('submit')
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#picked-result').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

</script>
@endpush