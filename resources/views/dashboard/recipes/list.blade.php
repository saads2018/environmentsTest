@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Recipes</h3>

        <div class="page-header-btn">
          <a href="{{route('recipe.create-form')}}">
            <img src="{{ Vite::asset('resources/images/dashboard/add.svg') }}" alt="Icon">
            <span>Add recipe</span>
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
          @foreach ($recipes as $recipe)
          <a href="{{route('edit-recipe', ['id' => $recipe->id])}}" class="grid-item">
            <div class="grid-item-content">
              <h6>{{$recipe->title}}</h6>

              <div class="grid-item-content-icon">
                <img src="{{ Vite::asset('resources/images/dashboard/time.svg') }}" alt="Icon">
                <span>{{$recipe->cook_time}} min</span>
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