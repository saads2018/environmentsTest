<div class="flash-container">

    @if ($message = Session::get('success'))
    <div class="flash-message jq-msg" data-type="success" data-timeout="8000" data-progress>{{ $message }}</div>
    @endif


    @if ($message = Session::get('error'))
    <div class="flash-message jq-msg" data-type="error" data-timeout="20000" data-progress>{{ $message }}</div>
    @endif

    @if ($message = Session::get('warning'))
    <div class="flash-message jq-msg" data-type="warning" data-timeout="15000" data-progress>{{ $message }}</div>
    @endif


    @if ($message = Session::get('info'))
    <div class="flash-message jq-msg" data-type="info" data-timeout="10000" data-progress>{{ $message }}</div>

    @endif


    @if ($errors->any())
    <div class="flash-message jq-msg" data-type="error" data-timeout="10000" data-progress>Please check the form below for errors</div>
    @endif
</div>
