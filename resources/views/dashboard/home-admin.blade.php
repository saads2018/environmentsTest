@extends('layouts.dashboard')

@section('content')
<main>
    <div class="wrapper">
      <header>
        <h4>Welcome, {{$user->name}}</h4>
        <span>{{ \Carbon\Carbon::now()->toFormattedDayDateString() }}</span>
      </header>

      <div class="overview">
        <div class="overview-title">Manage clinic list</div>

        <div class="overview-list">
          <div class="overview-item">
            <div class="overview-item-title">Clinics</div>
            <ul>
              @foreach($clinics as $clinic)
              <li>{{$clinic->name}}</li>
              @endforeach
            </ul>
            <a href="{{route('clinic.list')}}">
              <span>View more</span>
              <img src="{{ Vite::asset('resources/images/dashboard/arrow.svg') }}">
            </a>
          </div>
          <div class="overview-item">
            <div class="overview-item-title">Patients(wip)</div>
            <ul>
              <li>Howard Aarons</li>
              <li>Edward Alvarez</li>
              <li>Emily Atilla</li>
            </ul>
            <a href="{{route('patient.list')}}">
              <span>View more</span>
              <img src="{{ Vite::asset('resources/images/dashboard/arrow.svg') }}">
            </a>
          </div>
          <div class="overview-item">
            <div class="overview-item-title">Diets</div>
            <ul>
            @foreach($diets as $diet)
              <li>{{$diet->title}}</li>
            @endforeach
            </ul>
            <a href="{{route('diet.list')}}">
              <span>View more</span>
              <img src="{{ Vite::asset('resources/images/dashboard/arrow.svg') }}">
            </a>
          </div>
          <div class="overview-item">
            <div class="overview-item-title">Quizzes</div>
            <ul>
            @foreach($quizzes as $quiz)
              <li>{{$quiz->title}}</li>
            @endforeach
            </ul>
            <a href="{{route('quiz.list')}}">
              <span>View more</span>
              <img src="{{ Vite::asset('resources/images/dashboard/arrow.svg') }}">
            </a>
          </div>
        </div>
      </div>

      <div class="bottom-block">
        <div class="last-results">
          <a href="{{route('result.list')}}">Last results</a>

          <table>
            <thead>
              <tr>
                <th>Result name</th>
                <th>Patient</th>
                <th>Clinic</th>
                <th>Age</th>
              </tr>
            </thead>
            <tbody>
              @foreach($labResults as $result)
              <tr>
                <td>{{$result->name}}</td>
                <td>{{$result->patient->user->first_name}} {{$result->patient->user->last_name}}</td>
                <td>{{$result->clinicName}}</td>
                <td>{{$result->patient->age}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="right-block">
          <div class="right-block-item">
            <div class="right-block-title">Recipes</div>
            <ul>
              @foreach($recipes as $recipe)
                <li>{{$recipe->title}}</li>
              @endforeach
            </ul>
            <a href="{{route('recipe.list')}}">
              <span>View more</span>
              <img src="{{ Vite::asset('resources/images/dashboard/arrow-blue.svg') }}">
            </a>
          </div>
          
          <div class="right-block-item">
            <div class="right-block-title">Quotes</div>
            <ul>
              @foreach($quotes as $quote)
                <li>{{$quote->text}}</li>
              @endforeach
            </ul>
            <a href="{{route('quote.list')}}">
              <span>View more</span>
              <img src="{{ Vite::asset('resources/images/dashboard/arrow-blue.svg') }}">
            </a>
          </div>
        </div>
      </div>
    </div>
  </main>
  @endsection