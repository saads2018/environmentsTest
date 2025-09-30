@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Add diet</h3>
            </div>

            <div class="add-wrapper full">
                <form action="{{route('diet.create')}}" method="post" enctype="multipart/form-data">
                    @method('POST')
                    @csrf
                        <div class="add-wrapper-item row codes">
                            <label for="">Medical codes</label>
                            <select class="add-multiselect" name="codes[]" multiple="multiple" id="med_codes_select" style="width: 50%">
                            @foreach($medCodes as $code)
                                <option value="{{$code['id']}}">{{$code['value']}}</option>
                            @endforeach
                            </select>
                            @error('codes')
                                <div class="error-message">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="add-wrapper-item photo">
                            <input type="file" class="form-control" id="img-picker" name="image" style="display:none" accept="image/*" />
                            <div class="add-wrapper-item-btn upload-photo">
                                <img id="picked-result" src="{{ Vite::asset('resources/images/dashboard/upload.svg') }}" alt="Icon">
                                <button type="button">Upload photo</button>
                            </div>

                            <div class="uploaded-img">
                                <img src="{{ Vite::asset('resources/images/dashboard/naviwell-logo.png') }}" alt="Naviwell logo">
                            </div>
                        </div>
                        
                        <div class="add-wrapper-inner">
                            <div class="add-wrapper-item">
                                <label for="diet">Diet Title</label>
                                <input name="title" type="text" value="{{ old('title') }}" class="@error('title') error @enderror" />
                                @error('title')
                                    <div class="error-message">{{$message}}</div>
                                @enderror
                            </div>
                            
                        </div>
                    

                    <div class="add-diet-day-grid">
                    @if (!is_null(old('days-breakfast')))
                        @foreach (old('days-breakfast') as $key => $breakfast)
                            <div class="item">
                            <button class="remove remove-ingredient">Remove Day</button>
                                <div class="day-count">Day</div>

                                <div class="add-wrapper-item">
                                    <label>Snack</label>
                                    <textarea name="days-morning-snack[]" placeholder="morning snack">{{old('days-morning-snack')[$key]}}</textarea>
                                </div>

                                <div class="add-wrapper-item">
                                    <label>Breakfast</label>
                                    <textarea name="days-breakfast[]" placeholder="e.g. Greek yogurt with strawberries and chia seeds">{{$breakfast}}</textarea>
                                </div>

                                <div class="add-wrapper-item">
                                    <label>Afternoon snack</label>
                                    <textarea name="days-afternoon-snack[]" placeholder="afternoon snack">{{old('days-afternoon-snack')[$key]}}</textarea>
                                </div>

                                <div class="add-wrapper-item">
                                    <label>Lunch</label>
                                    <textarea name="days-lunch[]" placeholder="e.g. A whole grain sandwich with hummus and vegetables">{{old('days-lunch')[$key]}}</textarea>
                                </div>

                                <div class="add-wrapper-item">
                                    <label>Dinner</label>
                                    <textarea name="days-dinner[]" placeholder="e.g. A tuna salad with greens and olive oil, as well as a fruit salad">{{old('days-dinner')[$key]}}</textarea>
                                </div>
                            </div>
                        @endforeach

                    @else
                        <div class="item">
                            <button class="remove remove-ingredient">Remove Day</button>
                                <div class="day-count">Day</div>

                                <div class="add-wrapper-item">
                                    <label>Snack</label>
                                    <textarea name="days-morning-snack[]" placeholder="Morning snack"></textarea>
                                </div>

                                <div class="add-wrapper-item">
                                    <label>Breakfast</label>
                                    <textarea name="days-breakfast[]" placeholder="e.g. Greek yogurt with strawberries and chia seeds"></textarea>
                                </div>

                                <div class="add-wrapper-item">
                                    <label>Afternoon snack</label>
                                    <textarea name="days-afternoon-snack[]" placeholder="Afternoon snack"></textarea>
                                </div>

                                <div class="add-wrapper-item">
                                    <label>Lunch</label>
                                    <textarea name="days-lunch[]" placeholder="e.g. A whole grain sandwich with hummus and vegetables"></textarea>
                                </div>

                                <div class="add-wrapper-item">
                                    <label>Dinner</label>
                                    <textarea name="days-dinner[]" placeholder="e.g. A tuna salad with greens and olive oil, as well as a fruit salad"></textarea>
                                </div>
                            </div>
                        </div>
                    @endif
                    </div>

                    <div class="add-day-btn">
                        <img src="{{ Vite::asset('resources/images/dashboard/add-blue.svg') }}" alt="Icon">
                        <button type="button">Add Day</button>
                    </div>

                    <div class="add-wrapper-item add-diet-textarea">
                        <label for="description">Description</label>
                        <textarea name="description" class="@error('description') error @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>

                    <div class="add-wrapper-item pdf">
                        <input type="file" class="form-control" id="pdf-picker" name="attachment" style="display:none" accept="application/pdf">
                        <span id="pdfpicked-result"></span>
                        <div class="add-wrapper-item-btn upload-pdf">
                            <img id="picked-result" src="{{ Vite::asset('resources/images/dashboard/upload.svg') }}" alt="Icon">
                            <button type="button">Upload attachment</button>
                        </div>
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
    $(document).ready(function () {
        $(".remove").on('click', function(e) {
            e.preventDefault();
            if( $(this).parent().parent().children().length < 2 ) {
                return
            }
            $(this).parent().remove(); 
        })

        $('.add-multiselect').select2();


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