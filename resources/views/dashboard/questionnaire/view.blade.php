@extends('layouts.dashboard')

@section('content')
  <main>
    <div class="wrapper">
      <div class="page-header">
        <h3>Questionnaire results</h3>
      </div>

      <div class="questionnaire-wrapper">
        <div class="questionnaire-top">
          <div>Patient <span>{{$quiz->patient->user->first_name}} {{$quiz->patient->user->last_name}}</span></div>
          <button id="download-file">Download report</button>
        </div>

          @foreach($questions as $questionObj)
          <div class="questionnaire-inner">
            <div class="questionnaire-title">
              <span>{{$questionObj['title']}}</span>
            </div>
          
            <ol class="questionnaire-list">
              @foreach($questionObj['questions'] as $key => $question)
              <li>
                <div>{{$question}}</div>
                <span>
                  @if($quiz->answer_data[$questionObj['key']][$key] == 0)
                  Never
                  @elseif($quiz->answer_data[$questionObj['key']][$key] == 1)
                  Sometimes
                  @elseif($quiz->answer_data[$questionObj['key']][$key] == 2)
                  Often
                  @else
                  Always
                  @endif
                  </span>
              </li>
              @endforeach
            </ol>
          </div>
          @endforeach
      </div>
    </div>
  </main>
@endsection

@push('script')
<script>
    $(document).ready(function () {
        $('#download-file').on('click', function(e) {
          e.stopPropagation();
          const atag = document.createElement("a");
          $.get('{{$quiz->download}}', function(data, status){
            let tempUrl = data;
            atag.href = tempUrl;
            atag.target = '_blank'
            document.body.appendChild(atag);
            atag.click();
            document.body.removeChild(atag);
          });
        });
    });

</script>
@endpush