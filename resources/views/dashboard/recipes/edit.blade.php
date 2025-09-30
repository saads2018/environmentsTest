@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Edit recipe</h3>
            </div>

            <div class="add-wrapper full">
                <form action="{{route('recipe.update', ['id' => $recipe->id])}}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="">
                        <label for="med_codes_select">Medical codes</label>

                        <select class="add-multiselect" name="codes[]" multiple="multiple" id="med_codes_select" style="width: 50%">
                        @foreach($medCodes as $code)
                            <option value="{{$code['id']}}">{{$code['value']}}</option>
                        @endforeach
                        </select>
                        @error('codes')
                            <div class="error-message">{{$message}}</div>
                        @enderror

                        <div class="add-wrapper-item photo">
                            <input type="file" class="form-control" id="img-picker" name="image" style="display:none" accept="image/*" />
                            <div class="add-wrapper-item-btn upload-photo">
                            <img id="picked-result" src="{{ $recipe->imageUrl }}">
                                <button type="button">Upload photo</button>
                            </div>

                            <div class="uploaded-img">
                                <img src="{{ Vite::asset('resources/images/dashboard/naviwell-logo.png') }}" alt="Naviwell logo">
                            </div>
                        </div>

                        <div class="add-diet-inner">
                            <div class="add-wrapper-item">
                                <label for="diet">Recipe Title</label>
                                <input name="title" type="text" placeholder="e.g. Lemon chicken" value="{{$recipe->title}}" class="@error('title') error @enderror" />
                                @error('title')
                                    <div class="error-message">{{$message}}</div>
                                @enderror
                            </div>

                            <div class="add-wrapper-item">
                                <label for="diet">Servings</label>
                                <input name="servings" type="text" placeholder="e.g. 6" value="{{$recipe->servings}}" class="@error('servings') error @enderror" />
                                @error('servings')
                                    <div class="error-message">{{$message}}</div>
                                @enderror
                            </div>

                            <div class="add-wrapper-item">
                                <label for="duration">Cook Time</label>
                                <div>
                                    <input name="cook_time" type="number" placeholder="e.g. 20" value="{{$recipe->cook_time}}" class="@error('cook_time') error @enderror" />
                                    <span>min.</span>
                                    @error('cook_time')
                                    <div class="error-message">{{$message}}</div>
                                @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="add-diet-day-grid-item title">Add Ingredients</div>
                        <div class="add-diet-day-grid">
                            @foreach($recipe->ingredients as $ingredient)
                            <fieldset class="item">
                                <button class="remove remove-ingredient">Remove Ingredient</button>
                                <div class="add-wrapper-item">
                                    <label>Ingredient</label>
                                    <input type="text" name="ingredients[]" value="{{$ingredient}}" />
                                </div>
                            </fieldset>
                            @endforeach
                        </div>

                        <div class="add-day-btn add-ing-btn">
                            <img src="{{ Vite::asset('resources/images/dashboard/add-blue.svg') }}" alt="Icon">
                            <button class="add-ingredient" type="button">Add Ingredient</button>
                        </div>
                    </div>

                    <div class="steps-wrapper">
                        <div class="add-diet-day-grid-item title">How to cook</div>
                        <div class="add-diet-day-grid">
                            @foreach($recipe->steps as $step)
                            <fieldset class="item">
                                <button class="remove">Remove Step</button>
                                <div class="add-wrapper-item">
                                    <label>Step</label>
                                    <textarea name="steps[]">{{$step}}</textarea>
                                </div>
                            </fieldset>
                            @endforeach
                        </div>


                        <div class="add-day-btn">
                            <img src="{{ Vite::asset('resources/images/dashboard/add-blue.svg') }}" alt="Icon">
                            <button type="button">Add Step</button>
                        </div>
                    </div>

                    <div class="add-wrapper-item pdf">
                        <input type="file" class="form-control" id="pdf-picker" name="attachment" style="display:none" accept="application/pdf">
                        <span id="pdfpicked-result"></span>
                        @if($recipe->attachmentUrl)
                        <a href="{{$recipe->attachmentUrl}}" target="_blank">Download attachment</a>
                        @endif
                        <div class="add-wrapper-item-btn upload-pdf">
                            <img id="picked-result" src="{{ Vite::asset('resources/images/dashboard/upload.svg') }}" alt="Icon">
                            <button type="button">Upload attachment</button>
                        </div>
                    </div>

                    <button class="add-btn">Save</button>
                </form>
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
    $(document).ready(function () {
        $(".remove").on('click', function(e) {
            e.preventDefault();
            if( $(this).parent().parent().children().length < 2 ) {
                return
            }
            $(this).parent().remove(); 
        })

        $('.add-multiselect').select2();

        $('.add-multiselect').val(@json($recipe->codes)); 
        $('.add-multiselect').trigger('change');

        $(".upload-photo").on('click', function(e) {
            e.stopPropagation();
            $('#img-picker').click()
        });

        $(".upload-pdf").on('click', function(e) {
            e.stopPropagation();
            $('#pdf-picker').click()
        });

        $("#img-picker").change(function(){
            readURL(this);
        });

        $("#pdf-picker").change(function(){
            readPdfURL(this);
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

    function readPdfURL(input) {
        if (input.files && input.files[0]) {
            $('#pdfpicked-result').html(input.files[0].name)
        }
    }

    $(document).on('click','.add-day-btn', function(e){
        e.preventDefault();
        let ingItem = $(this).parent().find(".item").first().clone()
        ingItem.find('input').val('')
        ingItem.find('textarea').val('')
        $(this).parent().find(".add-diet-day-grid").append(ingItem)
        
        $(this).parent().find(".remove").click(function(e) {
            e.preventDefault();
            if( $(this).parent().parent().children().length < 2 ) {
                return
            }
            $(this).parent().remove(); 
        });
    });


</script>
@endpush