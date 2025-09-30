@include('partials.dashboard.header')

<body class="body">
<div class="app-container">
    @include('partials.dashboard.sidebar')

    @yield('content')
    @include('partials.dashboard.flash')
</div>

@include('partials.dashboard.footer')