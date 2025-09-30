@extends('layouts.dashboard')

@section('content')
<main>
  <div class="wrapper">
    <header>
      <h4>Welcome, {{$user->name}}</h4>
      <span>{{ \Carbon\Carbon::now()->toFormattedDayDateString() }}</span>
    </header>

    <div class="overview owner-overview">
      <h6>Clinic information</h6>
      <form action="{{route('clinic.update.owner')}}" method="post" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="add-wrapper-item photo">
            <input type="file" class="form-control" id="img-picker" name="logo" style="display:none" accept="image/*" />
            <div class="add-wrapper-item-btn upload-photo">
              <img id="picked-result" src="{{ $clinic->logoUrl ?: Vite::asset('resources/images/dashboard/upload.svg') }}" alt="Clinic logo">
              <button type="button">Upload logo</button>
            </div>
        </div>

        <label for="address">
          Address
          <input type="text" name="address" placeholder="Address" value="{{$clinic->address}}">
        </label>

        <label for="description">
          Description
          <textarea name="description" id="" cols="30" rows="10" placeholder="Description">{{$clinic->description}}</textarea>
        </label>

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