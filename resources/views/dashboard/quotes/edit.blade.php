@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Edit quote</h3>
            </div>

            <div class="add-wrapper full">
                <form action="{{route('quote.update', ['id' => $quote->id])}}" method="post">
                    @method('PUT')
                    @csrf
                    <div class="add-wrapper-item add-grid-item">
                        <textarea name="text" placeholder="Write a quote...">{{$quote->text}}</textarea>
                        @error('text')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>
                    <button class="add-btn">Save</button>
                </form>
            </div>
        </div>
    </main>
@endsection