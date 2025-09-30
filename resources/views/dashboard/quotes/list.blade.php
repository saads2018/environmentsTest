@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Quotes</h3>

        <div class="page-header-btn">
          <a href="{{route('quote.create-form')}}">
            <img src="{{ Vite::asset('resources/images/dashboard/add.svg') }}" alt="Icon">
            <span>Add quote</span>
          </a>
        </div>
      </div>

      <div class="search-wrapper">
        <form method="get">
          <label>
            <input placeholder="Search" type="search" autocomplete="off">
            <SearchIcon />
          </label>
        </form>
      </div>

      <div class="flex-wrapper">
        <div class="grid grid-two">
        @foreach($quotes as $quote)
          <a href="{{route('edit-quote', ['id' => $quote->id])}}" class="grid-item">
            <div class="grid-item-content">
              <h6>{{ $quote->text }}</h6>

              <div class="grid-item-content-icon">
                <img src="{{ Vite::asset('resources/images/dashboard/date.svg') }}" alt="Icon">
                <span>{{ $quote->scheduled_at->format('m/d/Y') }}</span>
              </div>
            </div>
            <div class="grid-item-btn">Edit</div>
          </a>
          @endforeach
        </div>
      </div>
    </div>
  </main>
@endsection