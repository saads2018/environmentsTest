@extends('layouts.dashboard')

@section('content')
    <main>
        <div class="wrapper">
            <div class="page-header">
                <h3>Edit Education & Quiz</h3>
            </div>

            <div class="add-wrapper full">
                <form action="{{route('quiz.update', ['id' => $quiz->id])}}" method="post" class="addquiz-inner" id="quiz_form">
                    @method('PUT')
                    @csrf

                    <label for="">Medical codes</label> 
                        <select class="add-multiselect" name="codes[]" multiple="multiple" id="med_codes_select" style="width: 50%">
                        @foreach($medCodes as $code)
                            <option value="{{$code['id']}}">{{$code['value']}}</option>
                        @endforeach
                        </select>
                        @error('codes')
                            <div class="error-message">{{$message}}</div>
                        @enderror

                    <div class="add-wrapper-item">
                        <label>Quiz Title</label>
                        <input type="text" name="title" placeholder="Write a title..." value="{{$quiz->title}}" class="@error('title') error @enderror" />
                        @error('title')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>

                    <div class="editor-container">
                        <textarea name="article" id="ckeditor">{{$quiz->article}}</textarea>
                        @error('article')
                            <div class="error-message">{{$message}}</div>
                        @enderror
                    </div>

                    <h4>Questions</h4>

                        <div class="addquiz-inner-grid">
                        @foreach($quiz->questions as $qId => $question)
                            <fieldset class="addquiz-inner-item">
                                <div class="addquiz-question">
                                    <div class="question-no">Question {{$qId+1}}</div>
                                    <button type="button" class="remove">Remove Question</button>
                                </div>

                                <textarea name="questions[{{$qId}}][text]" placeholder="Write a question...">{{$question['text']}}</textarea>

                                <div class="addquiz-inner-item-list">
                                    <span>Answers</span>
                                        <div class="answer-list">
                                            @foreach($question['answers'] as $key => $answer)
                                            <fieldset class="answer-item">
                                                <input type="radio" name="questions[{{$qId}}][correct]" value="{{$key}}" {{$key == ($question["correct"] ?? 0) ? 'checked' : ''}} />
                                                <input type="text" name="questions[{{$qId}}][answers][{{$key}}]" placeholder="Question answer..." value="{{$answer}}" />

                                                <button class="remove-answer" type="button">X</button>
                                            </fieldset>
                                            @endforeach
                                        </div>
                                        <button class="add-answer" type="button">Add Another Answer</button>
                                </div>
                            </fieldset>
                            @endforeach
                        </div>

                        <div class="add-day-btn">
                            <img src="{{ Vite::asset('resources/images/dashboard/add-blue.svg') }}" alt="Icon">
                            <button type="button">Add Question</button>
                        </div>

                    <button class="add-btn">Save</button>
                </Form>
            </div>
        </div>
    </main>
</template>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/37.1.0/classic/ckeditor.js"></script>

<script>
    $(document).ready(function () {

        // $('.ckeditor').ckeditor();
        ClassicEditor
            .create( document.querySelector( '#ckeditor' ) )
            .catch( error => {
                console.error( error );
            } );

        //removing question block
        $(".remove").on('click', function(e) {
            e.preventDefault();
            if( $(this).parent().parent().children().length < 2 ) {
                return
            }
            let questionsP = $(this).parent().parent().parent();
            $(this).parent().parent().remove(); 
            recountQuestions(questionsP)
            
        })

        //removing single answer from list
        $(".remove-answer").on('click', function(e) {
            e.preventDefault();
            if( $(this).parent().parent().children().length < 2 ) {
                return
            }
            let answerList = $(this).parent().parent()
            $(this).parent().remove(); 
            recountAnswerValues(answerList)
        })

        $('.add-multiselect').select2();

        $('.add-multiselect').val(@json($quiz->codes)); 
        $('.add-multiselect').trigger('change');

    });

    function recountQuestions(parent_item) {
        let totalQuestionsCount = $(parent_item).children().length;

        $(parent_item).children().each(function(p_index,p_item){

            $(p_item).find('.question-no').html(`Question ${p_index+1}`)

            $(p_item).find('textarea').attr('name', `questions[${p_index}][text]`)

            $(p_item).find('input:radio').each(function(index,item){
                $(item).attr('name', `questions[${p_index}][correct]`)
            })

            $(p_item).find('input:text').each(function(index,item){
                $(item).attr('name', `questions[${p_index}][answers][${index}]`)
            })
        });
    }

    function recountAnswerValues(parent_item) {
        let totalQuestionsCount = $(parent_item).parent().parent().index();
        parent_item.find('input:radio').each(function(index,item){
            $(item).val(index);
            $(item).attr('name', `questions[${totalQuestionsCount}][correct]`)
        })

        parent_item.find('input:text').each(function(index,item){
            $(item).attr('name', `questions[${totalQuestionsCount}][answers][${index}]`)
        })
    }

    $(document).on('click','.add-answer', function(e){
        e.preventDefault();
        let ingItem = $(this).parent().find(".answer-item").first().clone()

        let qCount = $(this).parent().parent().index();
        let ansCount = $(this).parent().find(".answer-list").children().length;

        ingItem.find('input:text').val('')
        ingItem.find('input:text').attr('name', `questions[${qCount}][answers][${ansCount}]`)
        ingItem.find('input:radio').removeAttr('checked');
        ingItem.find('input:radio').val(ansCount);
        ingItem.find('input:radio').attr('name', `questions[${qCount}][correct]`);


        $(this).parent().find(".answer-list").append(ingItem)

        //removing single answer from list
        $(".remove-answer").on('click', function(e) {
            e.preventDefault();
            if( $(this).parent().parent().children().length < 2 ) {
                return
            }

            let answerList = $(this).parent().parent()
            $(this).parent().remove(); 
            recountAnswerValues(answerList)

        })
    });


    $(document).on('click','.add-day-btn', function(e){
        e.preventDefault();
        let ingItem = $(this).parent().find(".addquiz-inner-item").first().clone()

        let totalQuestionsCount = $(this).parent().find(".addquiz-inner-grid").children().length;

        ingItem.find('.question-no').html(`Question ${totalQuestionsCount+1}`)

        ingItem.find('textarea').val('')
        ingItem.find('textarea').attr('name', `questions[${totalQuestionsCount}][text]`)

        ingItem.find('input:radio').each(function(index,item){
            $(item).removeAttr('checked');
            $(item).attr('name', `questions[${totalQuestionsCount}][correct]`)
        })

        ingItem.find('input:text').each(function(index,item){
            $(item).val('')
            $(item).attr('name', `questions[${totalQuestionsCount}][answers][${index}]`)
        })


        $(this).parent().find(".addquiz-inner-grid").append(ingItem)
        
        //removing question block
        $(this).parent().find(".remove").click(function(e) {
            e.preventDefault();
            if( $(this).parent().parent().children().length < 2 ) {
                return
            }
            let questionsP = $(this).parent().parent().parent();
            $(this).parent().parent().remove(); 
            recountQuestions(questionsP)

        });

        $(".remove-answer").on('click', function(e) {
            e.preventDefault();
            if( $(this).parent().parent().children().length < 2 ) {
                return
            }
            let answerList = $(this).parent().parent()
            $(this).parent().remove(); 
            recountAnswerValues(answerList)
        })
    });


</script>
@endpush