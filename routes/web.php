<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\RecipesController;
use App\Http\Controllers\Dashboard\DietsController;
use App\Http\Controllers\Dashboard\QuizController;
use App\Http\Controllers\Dashboard\QuotesController;
use App\Http\Controllers\Dashboard\ClinicsController;
use App\Http\Controllers\Dashboard\PatientsController;
use App\Http\Controllers\Dashboard\ResultsController;
use App\Http\Controllers\Dashboard\UsersController;
use App\Http\Controllers\Dashboard\StatisticsController;
use App\Http\Controllers\Dashboard\ClientsController;
use App\Http\Controllers\Dashboard\QuestionnaireController;
use App\Http\Controllers\Dashboard\AppointmentsController;

use App\Helpers\AWSHelper;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false, 'logout' => false]);


Route::middleware('guest')->get('/login', function () { return view('auth.dashboard.login'); })->name('login');
Route::middleware('guest')->post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::middleware('guest')->get('/password/forgot', function () { return view('auth.passwords.email'); })->name('password.request'); 
Route::middleware('guest')->get('/password/reset', function () { return view('auth.passwords.reset'); })->name('cognito.form.reset.password.code');

Route::middleware('aws-cognito')->get('/password/change', function () { return view('auth.passwords.change'); })->name('cognito.form.change.password');
Route::middleware('aws-cognito')->post('/password/change', [App\Http\Controllers\Auth\ChangePasswordController::class, 'actionChangePassword'])->name('cognito.action.change.password');
Route::middleware('aws-cognito')->any('logout', function (\Illuminate\Http\Request $request) { 
    $request->session()->flush();
    foreach (Cookie::get() as $key => $item){
        cookie($key, null, -2628000, null, null);
    } 
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');


Route::get('/', [HomeController::class, 'index'])->name('admin.home');

Route::group([
    'prefix'=>'recipes',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [RecipesController::class, 'list'])->name('recipe.list');
    Route::get('/new', [RecipesController::class, 'createForm'])->name('recipe.create-form');
    Route::post('/new', [RecipesController::class, 'create'])->name('recipe.create');
    Route::get('/{id}', [RecipesController::class, 'view'])->name('edit-recipe');
    Route::put('/{id}', [RecipesController::class, 'update'])->name('recipe.update');
});

Route::group([
    'prefix'=>'diet',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [DietsController::class, 'list'])->name('diet.list');
    Route::get('/new', [DietsController::class, 'createForm'])->name('diet.create-form');
    Route::post('/new', [DietsController::class, 'create'])->name('diet.create');
    Route::get('/{id}', [DietsController::class, 'view'])->name('edit-diet');
    Route::put('/{id}', [DietsController::class, 'update'])->name('diet.update');
});

Route::group([
    'prefix'=>'quiz',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [QuizController::class, 'list'])->name('quiz.list');
    Route::get('/new', [QuizController::class, 'createForm'])->name('quiz.create-form');
    Route::post('/new', [QuizController::class, 'create'])->name('quiz.create');
    Route::post('/order', [QuizController::class, 'setOrder'])->name('quiz.order');
    Route::get('/{id}', [QuizController::class, 'view'])->name('edit-quiz');
    Route::put('/{id}', [QuizController::class, 'update'])->name('quiz.update');
});

Route::group([
    'prefix'=>'quotes',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [QuotesController::class, 'list'])->name('quote.list');
    Route::get('/new', function () { return view('dashboard.quotes.create'); })->name('quote.create-form');
    Route::post('/new', [QuotesController::class, 'create'])->name('quote.create');
    Route::get('/{id}', [QuotesController::class, 'view'])->name('edit-quote');
    Route::put('/{id}', [QuotesController::class, 'update'])->name('quote.update');
});

Route::group([
    'prefix'=>'clinic',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [ClinicsController::class, 'list'])->name('clinic.list');
    Route::get('/new', function () { return view('dashboard.clinics.create'); })->name('clinic.create-form');
    Route::post('/new', [ClinicsController::class, 'create'])->name('clinic.create');
    Route::get('/{id}', [ClinicsController::class, 'view'])->name('edit-clinic');
    Route::put('/{id}', [ClinicsController::class, 'update'])->name('clinic.update');
    
    Route::post('/{clinicId}/reset', [ClinicsController::class, 'resetPassword'])->name('pwd.reset.admin');
    Route::post('/{clinicId}/delete', [ClinicsController::class, 'delete'])->name('clinic.delete');
});

Route::put('/clinic/', [ClinicsController::class, 'updateOwner'])->name('clinic.update.owner');

Route::group([
    'prefix'=>'patients',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [PatientsController::class, 'list'])->name('patient.list');
    Route::get('/new', [PatientsController::class, 'createForm'])->name('patient.create');
    Route::post('/new', [PatientsController::class, 'createPatient'])->name('patient.create.post');
    Route::get('/{clinicId}/{patientId}', [PatientsController::class, 'view'])->name('edit-patient');
    Route::put('/{clinicId}/{id}', [PatientsController::class, 'update'])->name('patient.update');
    Route::post('/{clinicId}/{patientId}/reset', [PatientsController::class, 'adminResetPassword'])->name('patients.reset.admin');
});

Route::group([
    'prefix'=>'results',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [ResultsController::class, 'list'])->name('result.list');
    Route::get('/{clinicId}/new', [ResultsController::class, 'createForm'])->name('result.create.form');
    Route::post('/{clinicId}/new', [ResultsController::class, 'create'])->name('result.create');
    Route::get('/{clinicId}/{resultId}', [ResultsController::class, 'view'])->name('edit-result');
    Route::put('/{clinicId}/{resultId}', [ResultsController::class, 'update'])->name('result.update');
});


Route::group([
    'prefix'=>'patient',
    'middleware' => ['aws-cognito']
], function() {
    Route::get('/', [PatientsController::class, 'ownerList'])->name('patient.list.owner');
    Route::get('/{patientId}', [PatientsController::class, 'ownerView'])->name('patient.edit.owner');
    Route::post('/{patientId}/reset', [PatientsController::class, 'resetPassword'])->name('patients.reset.owner');
    // Route::put('/{id}', [PatientsController::class, 'update'])->name('patient.update.owner');
});

Route::group([
    'prefix'=>'users',
    'middleware' => ['aws-cognito']
], function() {
    Route::get('/', [UsersController::class, 'ownerList'])->name('users.list.owner');
    Route::get('/create', function () { return view('ownerDashboard.users.create'); })->name('users.new.owner');
    Route::post('/create', [UsersController::class, 'create'])->name('users.create.owner');
    Route::get('/{physicianId}', [UsersController::class, 'ownerView'])->name('users.edit.owner');
    Route::post('/{physicianId}/reset', [UsersController::class, 'resetPassword'])->name('users.reset.owner');
    Route::post('/{physicianId}/delete', [UsersController::class, 'deleteUser'])->name('users.delete.owner');
    Route::put('/{id}', [UsersController::class, 'update'])->name('users.update.owner');

    Route::get('/{clinicId}/create', [UsersController::class, 'createUserAdminForm'])->name('users.new.admin');
    Route::post('/{clinicId}/create', [UsersController::class, 'createUserAdmin'])->name('users.create.admin');
    Route::get('/{clinicId}/{physicianId}', [UsersController::class, 'adminView'])->name('users.edit.admin');
    Route::post('/{clinicId}/{physicianId}/reset', [UsersController::class, 'adminResetPassword'])->name('users.reset.admin');
    Route::post('/{clinicId}/{physicianId}/delete', [UsersController::class, 'adminDeleteUser'])->name('users.delete.admin');
    Route::put('/{clinicId}/{id}', [UsersController::class, 'adminUpdate'])->name('users.update.admin');
});

Route::group([
    'prefix'=>'clients',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [ClientsController::class, 'list'])->name('clients.list');
    Route::get('/create', function () { return view('dashboard.clients.create'); })->name('clients.new');
    Route::post('/create', [ClientsController::class, 'create'])->name('clients.create');
    Route::get('/{userId}', [ClientsController::class, 'view'])->name('clients.edit');
    Route::post('/{userId}/reset', [ClientsController::class, 'resetPassword'])->name('clients.reset');
    Route::post('/{userId}/delete', [ClientsController::class, 'deleteUser'])->name('clients.delete');
    Route::put('/{userId}', [ClientsController::class, 'update'])->name('clients.update');
});

Route::group([
    'prefix'=>'stats',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [StatisticsController::class, 'list'])->name('stats.list');
});

Route::group([
    'prefix'=>'questionnaires',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [QuestionnaireController::class, 'list'])->name('questionnaire.list');
    Route::get('/{clinicId}/{quizId}', [QuestionnaireController::class, 'view'])->name('questionnaire.view');
});

Route::group([
    'prefix'=>'appointments',
    'middleware' => ['role:admin', 'aws-cognito']
], function() {
    Route::get('/', [AppointmentsController::class, 'list'])->name('appointments.list');
});


Route::get('file/{params?}', function ($params = null) { 
    if($params) {
        $helper = new AWSHelper();
        return $helper->downloadLinkFile($params);
    }
    return null;   
})->where('params', '(.*)')->name('file.download');