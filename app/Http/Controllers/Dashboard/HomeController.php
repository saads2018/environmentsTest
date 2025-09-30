<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Tenant;
use App\Models\Tenant\Diet;
use App\Models\Tenant\LabResult;
use App\Models\Tenant\Quiz;
use App\Models\Tenant\Quote;
use App\Models\Tenant\Recipe;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;


class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $user = auth()->guard('web')->user();

        if($user->hasRole('admin')){

            $clinics =  Tenant::latest()->take(3)->get();
            $diets = Diet::latest()->take(3)->get();
            $recipes = Recipe::latest()->take(3)->get();
            $quizzes = Quiz::latest()->take(3)->get();
            $quotes = Quote::latest()->take(3)->get();
    
            $labResults = new EloquentCollection();
            foreach ($clinics as $clinic) {
                $latestResults = $clinic->run(function () {
                    return LabResult::with('patient')->latest()->take(2)->get();
                }); 
                foreach ($latestResults as $result) {
                    $result->clinicName = $clinic->name;
                    $labResults->push($result);
                }
            }
            return view('dashboard.home-admin')->with(compact('user', 'clinics', 'diets', 'recipes', 'quizzes', 'quotes', 'labResults'));

        } else if($user->tenant) {
            $clinic = Tenant::where('user_id', $user->id)->firstOrFail();
            return view('dashboard.home')->with(compact('user', 'clinic'));
        } else {
            return response('You have no clinics to manage');
        }
        
    }


}
