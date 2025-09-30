@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Edit diagnostic result</h3>
            </div>

            <div class="add-wrapper">
            <form action="{{route('result.update', ['clinicId' => $clinic->id, 'resultId' => $result->id])}}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input hidden type="file" name="attachment"  id="file-picker"  />
                    <div class="add-wrapper-item upload-photo">
                        <label>Upload Result</label>
                        <div class="upload-btn">
                            <img src="{{ Vite::asset('resources/images/dashboard/attach.svg') }}" alt="Icon">
                        </div>
                        <a id="download-file" href="#" style="text-decoration: underline;">
                            <div class="result-file">{{$result->file['name']}}
                                <span>x</span>
                            </div>
                        </a>
                    </div>
                    
                    <div class="add-wrapper-item">
                        <label>Result Name</label>
                        <input name="name" type="text" placeholder="" value="{{$result->name}}" />
                    </div>

                    <div class="add-wrapper-item">
                        <label>Patient Name</label>
                        <input name="patient_id"  type="hidden" value="{{$result->patient->id}}" />
                        <input name="patient" type="text" placeholder="" value="{{$result->patient->user->first_name}} {{$result->patient->user->last_name}}" />
                    </div>

                    <div class="add-wrapper-item">
                        <label>Date
                            <input name="date" type="date" class="popup-content-item-input" value="{{$result->date->format('Y-m-d')}}" />
                        </label>
                    </div>

                    <!-- <button class="add-btn">Save</button> -->
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

        $('#download-file').on('click', function(e) {
            e.stopPropagation();
            const atag = document.createElement("a");
            atag.download = '{{$result->name}}';
            let clinicId = "{{$clinic->id}}";
            let patientId = "{{$result->patient->id}}"
            let resultId = "{{$result->id}}"
            let fileRef = "{{$result->file['ref']}}"
            let url = `/file/${clinicId}/results/${patientId}/${fileRef}`
            $.get(url, function(data, status){
                let tempUrl = data;
                atag.href = tempUrl;
                atag.target = '_blank'
                document.body.appendChild(atag);
                atag.click();
                document.body.removeChild(atag);
            });
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