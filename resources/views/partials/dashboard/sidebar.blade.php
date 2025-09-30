<div class="open-menu">
    <img src="{{ Vite::asset('resources/images/dashboard/menu.svg') }}" alt="Icon">
</div>

<aside>
    <div class="aside">
        <div class="logo">
        <img src="{{ Vite::asset('resources/images/dashboard/naviwell-logo.png') }}" alt="Naviwell logo">
        </div>

        <nav>
            <ul>
                <li>
                    <a href="{{route('admin.home')}}" class="{{ Route::currentRouteName() ==  'admin.home' ? 'active' : ''}}">
                        <img src="{{ Vite::asset('resources/images/dashboard/dashboard.svg') }}">
                        Dashboard
                    </a>
                </li>
                @role('admin')
                <li>
                    <a href="{{route('appointments.list')}}" class="{{ Route::currentRouteName() ==  'appointments.list' ? 'active' : ''}}">
                    <img src="{{ Vite::asset('resources/images/dashboard/appointments.svg') }}">
                        Appointments
                    </a>
                </li>
                <li>
                    <a href="{{route('clients.list')}}" class="{{ Route::currentRouteName() ==  'clients.owner' ? 'active' : ''}}">
                        <img src="{{ Vite::asset('resources/images/dashboard/doctors.svg') }}">
                        Users
                    </a>
                </li>
                <li>
                    <a href="{{route('clinic.list')}}" class="{{ Route::currentRouteName() ==  'clinic.list' ? 'active' : ''}}">
                    <img src="{{ Vite::asset('resources/images/dashboard/clinics.svg') }}">
                        Clinics 
                    </a>
                </li>
                <li>
                    <a href="{{route('patient.list')}}" class="{{ Route::currentRouteName() ==  'patient.list' ? 'active' : ''}}">
                    <img src="{{ Vite::asset('resources/images/dashboard/patients.svg') }}">
                        Patients
                    </a>
                </li>
                <li>
                    <a href="{{route('diet.list')}}" class="{{ Route::currentRouteName() ==  'diet.list' ? 'active' : ''}}">
                    <img src="{{ Vite::asset('resources/images/dashboard/diet.png') }}">
                        Diets
                    </a>
                </li>
                <li>
                    <a href="{{route('recipe.list')}}" class="{{ Route::currentRouteName() ==  'recipe.list' ? 'active' : ''}}">
                    <img src="{{ Vite::asset('resources/images/dashboard/recipe.png') }}">
                        Recipes
                    </a>
                </li>
                <li>
                    <a href="{{route('quote.list')}}" class="{{ Route::currentRouteName() ==  'quote.list' ? 'active' : ''}}">
                    <img src="{{ Vite::asset('resources/images/dashboard/quote.png') }}">
                        Quotes
                    </a>
                </li>
                <li>
                    <a href="{{route('quiz.list')}}" class="{{ Route::currentRouteName() ==  'quiz.list' ? 'active' : ''}}">
                    <img src="{{ Vite::asset('resources/images/dashboard/quiz.png') }}">
                        Quizzes
                    </a>
                </li>
                <li>
                    <a href="{{route('questionnaire.list')}}" class="{{ Route::currentRouteName() ==  'questionnaire.owner' ? 'active' : ''}}">
                        <img src="{{ Vite::asset('resources/images/dashboard/doctors.svg') }}">
                        Questionnaire Results
                    </a>
                </li>
                <li>
                    <a href="{{route('result.list')}}" class="{{ Route::currentRouteName() ==  'result.list' ? 'active' : ''}}">
                    <img src="{{ Vite::asset('resources/images/dashboard/lab-results.png') }}">
                        Diagnostic results
                    </a>
                </li>
                <li>
                    <a href="{{route('stats.list')}}">
                        <img src="{{ Vite::asset('resources/images/dashboard/statistics.svg') }}">
                        Statistics
                    </a>
                </li>
                @else
                <li>
                    <a href="{{route('users.list.owner')}}" class="{{ Route::currentRouteName() ==  'users.list.owner' ? 'active' : ''}}">
                        <img src="{{ Vite::asset('resources/images/dashboard/doctors.svg') }}">
                        Users
                    </a>
                </li>
                <li>
                    <a href="{{route('patient.list.owner')}}" class="{{ Route::currentRouteName() ==  'patient.list.owner' ? 'active' : ''}}">
                        <img src="{{ Vite::asset('resources/images/dashboard/patients.svg') }}">
                        Patients
                    </a>
                </li>
                @endrole
            </ul>
        </nav>

        <div class="footer">
            <a href="{{ route('logout') }}">
            <img src="{{ Vite::asset('resources/images/dashboard/logout.svg') }}" width="32px">
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

<script>
    var body = document.body;

    document.querySelector('.open-menu').onclick = function() {
        body.classList.add('active');
    };

    body.onclick = function(event) {
        if (event.target === body) {
            html.classList.remove('active');
        }
    };
</script>

<style>
    .open-menu {
        display: none;
        position: absolute;
        right: 0;
        z-index: 9;
    }

    .open-menu img {
        width: 32px;
        height: 32px;
        cursor: pointer;
    }

    aside {
        background-color: #F8F7FA;
        width: 350px;
        height: 100vh;
        padding: 28px 32px;
        position: fixed;
    }

    .aside {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .aside .logo img {
        max-width: 120px;
        margin: 0 auto 56px;
    }

    .aside nav ul li {
        margin-bottom: 8px;
    }

    .aside nav ul li a {
        padding: 8px 16px;
        display: flex;
        align-items: center;
        font-size: 18px;
        color: #000000;
        opacity: .6;
        transition: .3s ease;
    }

    .aside nav ul li:last-child {
        margin-bottom: 0;
    }

    .aside nav ul li a.active {
        /* background-color: #F3F4FD; */
        opacity: 1;
        /* border-radius: 16px; */
    }

    .aside nav ul li a svg, 
    .aside nav ul li a img {
        width: 24px;
        margin-right: 12px;
    }

    .footer {
        margin: auto 0 0 16px;
    }

    .footer a {
        display: flex;
        align-items: center;
        opacity: .6;
        transition: .3s ease;
    }

    .footer svg {
        transform: scale(0.8);
    }

    .footer span {
        margin-left: 8px;
        font-size: 18px;
    }

    @media (hover: hover) {
        .aside nav ul li a:hover, 
        .footer a:hover {
            /* background-color: #F3F4FD;
            color: #000000; */
            opacity: 1;
        }
    }

    @media screen and (max-width: 1199px) {
        aside {
            width: 280px;
        }
    }

    @media screen and (max-width: 768px) {
        .open-menu {
            display: block;
        }
        
        aside {
            width: 250px;
            position: absolute;
            transform: translateX(-350px);
            transition: .3s ease;
            z-index: 9;
        }

        body.active aside {
            transform: translate(0);
        }
    }
</style>