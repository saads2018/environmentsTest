<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

use App\Http\Controllers\Tenants\PatientController;
use App\Http\Controllers\Tenants\PhysicianController;

use App\Http\Controllers\Tenants\FamilyHistoryController;
use App\Http\Controllers\Tenants\MedicalController;
use App\Http\Controllers\Tenants\MedicineController;

use App\Http\Controllers\Tenants\AppointmentController;
use App\Http\Controllers\Tenants\HealthDataController;
use App\Http\Controllers\Tenants\QuoteController;
use App\Http\Controllers\Tenants\MessagesController;
use App\Http\Controllers\Tenants\DietController;
use App\Http\Controllers\Tenants\RecipeController;
use App\Http\Controllers\Tenants\QuizController;
use App\Http\Controllers\Tenants\QuestionnaireController;
use App\Http\Controllers\Tenants\LabResultsController;
use App\Http\Controllers\Tenants\ClinicalNotesController;
use App\Http\Controllers\Tenants\SoapController;


/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/
Route::group([
    'prefix' => '/{tenant}',
    'middleware' => [
        InitializeTenancyByPath::class
    ],
], function () {
    Route::get('/', function () {
        $tenant = tenant();
        $data = array(
            'name' => $tenant->name,
            'id' => $tenant->id,
            'description' => $tenant->description ? $tenant->description : '',
            'address' => $tenant->address ? $tenant->address : '',
            'logo' => $tenant->logo ? $tenant->logo : ''
        );
        return response()->json(['data' => $data], 200);
    });

    Route::post('login', [App\Http\Controllers\Tenants\AuthController::class, 'login']);
    Route::post('refresh', [App\Http\Controllers\Tenants\AuthController::class, 'refreshToken']);
    Route::middleware('aws-cognito')->get('user', [App\Http\Controllers\Tenants\AuthController::class, 'user']);
    Route::middleware('aws-cognito')->put('user', [App\Http\Controllers\Tenants\AuthController::class, 'updateUser']);

    Route::middleware('aws-cognito')->post('register', [App\Http\Controllers\Tenants\AuthController::class, 'register']);
    Route::middleware('aws-cognito')->get('logout', [App\Http\Controllers\Tenants\AuthController::class, 'logout']);
    Route::post('change-password', [App\Http\Controllers\Tenants\AuthController::class, 'actionChangePassword']);
    Route::post('reset-password', [App\Http\Controllers\Tenants\AuthController::class, 'resetPassword']);
    Route::post('set-password', [App\Http\Controllers\Tenants\AuthController::class, 'setPassword']);


    Route::group([
        'prefix'=>'patients',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [PatientController::class, 'index']);
        Route::post('/', [PatientController::class, 'store']);
        Route::post('/search', [PatientController::class, 'search']);
        Route::get('/data', [PatientController::class, 'getFormData']);
        Route::get('/{profileId}', [PatientController::class, 'show']);
        Route::delete('/{profileId}', [PatientController::class, 'delete']);
        Route::put('/{profileId}', [PatientController::class, 'update']);
        Route::get('/{profileId}/attach', [PatientController::class, 'attach'])->middleware('physician');
        Route::post('/{profileId}/condition', [PatientController::class, 'attachMedCondition']);
        Route::post('/{profileId}/history', [PatientController::class, 'attachFamilyHistory']);
        Route::get('/{profileId}/health-data', [HealthDataController::class, 'all']);
        Route::post('/{profileId}/health-data', [PatientController::class, 'attachHealthData']);
        Route::post('/{profileId}/questionnaire', [PatientController::class, 'attachQuestionaire']);
        Route::get('/{profileId}/report', [PatientController::class, 'report']);
        Route::get('/{profileId}/last-quiz', [PatientController::class, 'lastQuiz']);
        Route::get('/{profileId}/last-note', [PatientController::class, 'lastNote']);
        Route::post('/{profileId}/force-quiz', [PatientController::class, 'forceManualQuiz']);
        Route::post('/quiz', [PatientController::class, 'attachQuizResult']);
        Route::post('/{profileId}/email', [PatientController::class, 'sendEmail']);
    });

    Route::group([
        'prefix'=>'physicians',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [PhysicianController::class, 'index']);
        Route::post('/', [PhysicianController::class, 'store']);
        Route::post('/search', [PhysicianController::class, 'search']);
        Route::get('/{profileId}', [PhysicianController::class, 'show']);
        Route::get('/{profileId}/patients', [PhysicianController::class, 'patients']);
        Route::put('/{profileId}', [PhysicianController::class, 'update']);
    });

    Route::group([
        'prefix'=>'family-history',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [FamilyHistoryController::class, 'index']);
        Route::post('/', [FamilyHistoryController::class, 'store']);
        Route::get('/{id}', [FamilyHistoryController::class, 'show']);
        Route::put('/{id}', [FamilyHistoryController::class, 'update']);
    });

    Route::group([
        'prefix'=>'medical-conditions',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [MedicalController::class, 'index']);
        Route::post('/', [MedicalController::class, 'store']);
        Route::get('/{id}', [MedicalController::class, 'show']);
        Route::put('/{id}', [MedicalController::class, 'update']);
    });

    Route::group([
        'prefix'=>'medicine',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [MedicineController::class, 'index']);
        Route::post('/', [MedicineController::class, 'store']);
        Route::get('/{id}', [MedicineController::class, 'show']);
        Route::put('/{id}', [MedicineController::class, 'update']);
    });

    Route::group([
        'prefix'=>'health-data',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/{id}', [HealthDataController::class, 'show']);
    });

    Route::group([
        'prefix'=>'appointments',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [AppointmentController::class, 'index']);
        Route::post('/', [AppointmentController::class, 'store']);
        Route::get('/today', [AppointmentController::class, 'listToday']);
        Route::get('/{id}', [AppointmentController::class, 'show']);
        Route::put('/{id}', [AppointmentController::class, 'update']);
        Route::delete('/{id}', [AppointmentController::class, 'delete']);
    });

    Route::group([
        'prefix'=>'quotes',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [QuoteController::class, 'index']);
        Route::post('/', [QuoteController::class, 'store']);
        Route::post('/mass', [QuoteController::class, 'massStore']);
        Route::get('/day-quote', [QuoteController::class, 'quoteOfTheDay']);
        Route::get('/{id}', [QuoteController::class, 'show']);
        Route::put('/{id}', [QuoteController::class, 'update']);
        Route::delete('/{id}', [QuoteController::class, 'delete']);
    });

    Route::group([
        'prefix'=>'messages',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [MessagesController::class, 'index']);
        Route::get('/{patient_id}', [MessagesController::class, 'listForPatient']);
        Route::post('/', [MessagesController::class, 'store']);
    });

    Route::group([
        'prefix'=>'physician-messages',
        'middleware' => ['aws-cognito', 'physician']
    ], function() {
        Route::get('/', [MessagesController::class, 'physician_index']);
        Route::get('/{conversation_id}', [MessagesController::class, 'listForPhysician']);
        Route::post('/', [MessagesController::class, 'physician_store']);
    });

    Route::group([
        'prefix'=>'diet',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [DietController::class, 'index']);
        Route::post('/', [DietController::class, 'store']);
        Route::get('/{id}', [DietController::class, 'show']);
        Route::put('/{id}', [DietController::class, 'update']);
        Route::delete('/{id}', [DietController::class, 'delete']);
    });

    Route::group([
        'prefix'=>'recipes',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [RecipeController::class, 'index']);
        Route::post('/', [RecipeController::class, 'store']);
        Route::get('/{id}', [RecipeController::class, 'show']);
        Route::put('/{id}', [RecipeController::class, 'update']);
        Route::delete('/{id}', [RecipeController::class, 'delete']);
    });

    Route::group([
        'prefix'=>'quizzes',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [QuizController::class, 'index']);
        Route::post('/', [QuizController::class, 'store']);
        Route::put('/set-order', [QuizController::class, 'setOrder']);
        Route::get('/completed', [QuizController::class, 'completed']);
        Route::get('/{id}', [QuizController::class, 'show']);
        Route::put('/{id}', [QuizController::class, 'update']);
        Route::delete('/{id}', [QuizController::class, 'delete']);

    });

    Route::group([
        'prefix'=>'questionnaires',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [QuestionnaireController::class, 'index']);

    });

    Route::group([
        'prefix'=>'lab-results',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [LabResultsController::class, 'index']);
        Route::post('/', [LabResultsController::class, 'store']);
        Route::get('/{id}', [LabResultsController::class, 'show']);
        Route::put('/{id}', [LabResultsController::class, 'update']);
        Route::delete('/{id}', [LabResultsController::class, 'delete']);

    });

    Route::group([
        'prefix'=>'notes',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::get('/', [ClinicalNotesController::class, 'index']);
        Route::post('/', [ClinicalNotesController::class, 'store']);
        Route::get('pdf/{id}', [ClinicalNotesController::class, 'pdf']);
        Route::get('/{id}', [ClinicalNotesController::class, 'show']);
        Route::put('/{id}', [ClinicalNotesController::class, 'update']);
        Route::delete('/{id}', [ClinicalNotesController::class, 'delete']);

    });

    Route::group([
        'prefix'=>'soap',
        'middleware' => 'aws-cognito'
    ], function() {
        Route::post('/', [SoapController::class, 'store']);
        Route::get('/{id}', [SoapController::class, 'show']);
        Route::put('/{id}', [SoapController::class, 'update']);

    });


    Route::get('/dx-codes', function () {
        $codes = App\Enums\EducationCode::toReadableArray();
        $response = array_map(function($key, $value) {
            return ["id" => $key, "value" => $value];
          }, array_keys($codes), $codes);
        return $response;
    })->middleware('aws-cognito');

});