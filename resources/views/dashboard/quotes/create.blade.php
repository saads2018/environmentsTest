@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Add quote</h3>
            </div>

            <div class="add-wrapper full">
                <form action="{{route('quote.create')}}" method="post">
                    @method('POST')
                    @csrf
                    <div class="add-wrapper-item add-grid-item">
                        <textarea name="text[]" placeholder="Write a quote..."></textarea>
                    </div>

                    <div class="add-day-btn">
                        <img src="{{ Vite::asset('resources/images/dashboard/add-blue.svg') }}" alt="Icon">
                        <button type="button">Add New Quote</button>
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
        $(".remove").on('click', function(e) {
            e.preventDefault();
            if( $(this).parent().parent().children().length < 2 ) {
                return
            }
            $(this).parent().remove(); 
        })

    });

    $(document).on('click','.add-day-btn', function(e){
        e.preventDefault();
        let ingItem = $(this).parent().find("textarea").first().clone()
        ingItem.val('')
        $(this).parent().find(".add-grid-item").append(ingItem)
    });


</script>
@endpush