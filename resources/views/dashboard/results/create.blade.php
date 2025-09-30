@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Add diagnostic result for {{$clinic->name}} facility</h3>
            </div>

            <div class="add-wrapper">
            <form action="{{route('result.create', ['clinicId' => $clinic->id])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input hidden type="file" name="file"  id="file-picker"  />
                    <div class="add-wrapper-item upload-photo">
                        <label>Upload Result</label>
                        <div class="upload-btn">
                            <img src="{{ Vite::asset('resources/images/dashboard/attach.svg') }}" alt="Icon">
                        </div>
                        <a id="download-file" href="#" style="text-decoration: underline;">
                            <div class="result-file"></div>
                        </a>
                        @error('file')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    
                    <div class="add-wrapper-item">
                        <label>Result Name</label>
                        <input name="name" type="text" placeholder="" value="{{old('name')}}" class="@error('name') error @enderror" />
                        @error('name')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>

                    <div class="add-wrapper-item">
                        <label>Patient Name</label>
                        <select name="patient_id" class="@error('patient_id') error @enderror">
                            <option value="">Choose patient:</option>
                            @foreach($patients as $patient)
                            <option value="{{$patient->id}}">{{$patient->user->first_name}} {{$patient->user->last_name}}</option>
                            @endforeach 
                        </select>
                        @error('patient_id')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>

                    <div class="add-wrapper-item">
                        <label>Date
                            <input name="date" type="date" class="popup-content-item-input" value="{{old('date')}}" class="@error('date') error @enderror" />
                        </label>
                        @error('date')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>

                    <button class="add-btn">Save</button>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('script')
<script>
    $(document).ready(function () {

        $(".upload-photo").on('click', function(e) {
            e.stopPropagation();
            $('#file-picker').click()
        });

        $("#file-picker").change(function(){
            readURL(this);
        });

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            $('.result-file').html(input.files[0].name)

        }
    }


</script>
@endpush