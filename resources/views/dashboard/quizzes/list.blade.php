@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Quizzes</h3>

        <div class="page-header-btn">

        <a id="reorder" href="#">
            <span>Reorder quizzes</span>
          </a>

          <a href="{{route('quiz.create-form')}}">
            <img src="{{ Vite::asset('resources/images/dashboard/add.svg') }}" alt="Icon">
            <span>Add Education & Quiz </span>
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
        <ul class="quiz-list">
          @foreach($codes as $id => $dx_code)
          <li class="{{ $code ==  $id ? 'active' : ''}}"><a href="{{route('quiz.list', ['code' => $id])}}">{{$dx_code}}</a></li>
          @endforeach
        </ul>

        <div class="grid" id="quiz-grid">
          @foreach($quizzes as $key => $quiz)
          <a href="{{route('edit-quiz', ['id' => $quiz->id])}}" class="grid-item" data-id="{{$quiz->id}}">
            <div class="grid-item-content">
              <h6><span class="index">{{$key+1}}</span>. {{$quiz->title}}</h6>

              <div class="grid-item-content-icon">
                <img src="{{ Vite::asset('resources/images/dashboard/date.svg') }}" alt="Icon">
                <span>{{$quiz->created_at->format('m/d/Y')}}</span>
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


@push('script')
<script>
  $(document).ready(function () {

    let reorderBtn = document.getElementById('reorder');
    let grid = document.getElementById('quiz-grid');

    var sortable = Sortable.create(grid, {
      disabled: true,
      onChange(evt) {
        let items = grid.getElementsByClassName('grid-item');
        for (var i = 0; i < items.length; i++) {
          let e = items[i].querySelector('.index')
          e.innerText = i+1
        };
      }
    });

    reorderBtn.onclick = function() {
      var state = sortable.option("disabled");
	    sortable.option("disabled", !state);
      reorderBtn.getElementsByTagName('span')[0].innerText = state ? "Save" : "Reorder quizzes"

      if(!state) {
        saveQuizOrder()
      } else {
        reorderBtn.classList.toggle('save')
      }
    }


    function saveQuizOrder(params) {
      let grid = document.getElementById('quiz-grid');
      let children = grid.getElementsByTagName('a');
      let arr = [];
      for (var i = 0; i < children.length; i++) {
        arr.push({'id': children[i].getAttribute('data-id'), 'order': i+1});
      }

      let csrfToken = '{{ csrf_token() }}';

      let data = {'code': '{{$code}}', 'quizzes': arr}
      fetch("{{ route('quiz.order') }}", { 
        method: 'POST',
        body: JSON.stringify(data),
        headers:{
            'Content-Type': 'application/json',
            "X-CSRF-Token": csrfToken
        }
      }).then(() => window.FlashMessage.success('Quiz order has been saved.'));
    }

    

  });

</script>

@endpush