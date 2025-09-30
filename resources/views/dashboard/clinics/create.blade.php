@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Add clinic</h3>
            </div>

            <div class="add-wrapper">
                <form action="{{route('clinic.create')}}" method="post" enctype="multipart/form-data">
                    @method('POST')
                    @csrf
                    <div class="add-wrapper-item photo">
                            <input type="file" class="form-control" id="img-picker" name="logo" style="display:none" accept="image/*" />
                            <div class="add-wrapper-item-btn upload-photo">
                                <img id="picked-result" src="{{ Vite::asset('resources/images/dashboard/upload.svg') }}" alt="Icon">
                                <button type="button">Upload logo</button>
                            </div>

                            <div class="uploaded-img">
                                <img src="{{ Vite::asset('resources/images/dashboard/naviwell-logo.png') }}" alt="Naviwell logo">
                            </div>
                        </div>                 
                    <div class="add-wrapper-item">
                        <label for="userName">Owner's Full Name</label>
                        <input 
                            type="text"
                            name="name"
                            value="{{ old('name') }}" 
                            class="@error('name') error @enderror">
                        @error('name')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                        <label for="email">Owner's Email</label>
                        <input type="text" name="email" value="{{ old('email') }}" class="@error('email') error @enderror">
                        @error('email')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                        <label for="clinicName">Clinic name</label>
                        <input type="text" name="clinic-name" value="{{ old('clinic-name') }}" class="@error('clinic-name') error @enderror">
                        @error('clinic-name')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                        <div class="subdomain">
                            <label for="url">Subdomain</label>
                            @error('clinic-subdomain')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                            <input type="text" name="clinic-subdomain" value="{{ old('clinic-subdomain') }}" class="@error('clinic-subdomain') error @enderror">
                            
                        </div>
                    </div>
                    <div class="add-wrapper-item">
                        <label for="address">Clinic Address</label>
                        <input type="text" name="clinic-address" value="{{ old('clinic-address') }}" class="@error('clinic-address') error @enderror">
                        @error('clinic-address')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="add-wrapper-item">
                        <label for="description">Clinic Description</label>
                        <textarea name="clinic-description" class="@error('clinic-description') error @enderror">{{old('clinic-description')}}</textarea>
                        @error('clinic-description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>

                    <button class="add-btn">Save</button>
                </Form>
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