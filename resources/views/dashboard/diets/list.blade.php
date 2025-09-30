@extends('layouts.dashboard')

@section('content')
<main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Diets</h3>

        <div class="page-header-btn">
          <a href="{{route('diet.create-form')}}">
                <img src="{{ Vite::asset('resources/images/dashboard/add.svg') }}" alt="Icon">
                <AddIcon />
                <span>Add diet</span>
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
        <div class="grid">
          @foreach($diets as $diet)
          <a href="{{route('edit-diet', ['id' => $diet->id])}}" class="grid-item">
            <div class="grid-item-content">
              <h6>{{ $diet->title }}</h6>

              <div class="grid-item-content-icon">
                  <img src="{{ Vite::asset('resources/images/dashboard/weekly.svg') }}" alt="Icon">
                  <span>{{ count($diet->data['days']) }} days</span>
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