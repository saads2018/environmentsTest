<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Tenant\Recipe;

use Illuminate\Http\Request;
use App\Helpers\AWSHelper;

class RecipesController extends Controller
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
    public function list()
    {

        $recipes = Recipe::get();
        return view('dashboard.recipes.list')->with(compact('recipes'));;
    }

    public function createForm() {
        $codes = \App\Enums\EducationCode::toReadableArray();
        $medCodes = array_map(function($key, $value) {
            return ["id" => $key, "value" => $value];
          }, array_keys($codes), $codes);
        return view('dashboard.recipes.create')->with(compact('medCodes'));
    }


    public function view($id)
    {
        $recipe = Recipe::where('id', $id)->firstOrFail();
        $codes = \App\Enums\EducationCode::toReadableArray();
        $medCodes = array_map(function($key, $value) {
            return ["id" => $key, "value" => $value];
          }, array_keys($codes), $codes);
        return view('dashboard.recipes.edit')->with(compact('recipe', 'medCodes'));
    }

    public function create(Request $request) {

        $values = array_filter($request->validate([
            'title' => 'required|string|max:255',
            'servings' => 'required|integer',
            'cook_time' => 'required|integer',
            'image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
            'attachment' => 'sometimes|nullable|mimes:pdf',
            'ingredients' => 'required|array|min:1',
            'steps' => 'required|array|min:1',
            'codes' => 'required|array'
        ]));


        if(array_key_exists("image", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['image']->extension();
            $values['image']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadSharedFile($name, 'recipes');
            $values['image'] = 'shared-'.$name;

        } else {
            unset($values['image']);
        }

        if(array_key_exists("attachment", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['attachment']->extension();
            $values['attachment']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadSharedFile($name, 'recipes');
            $values['attachment'] = 'shared-'.$name;

        } else {
            unset($values['attachment']);
        }

        //make sure to remove empty object from nested arrays
        $values['ingredients'] = array_filter($values['ingredients']);
        $values['steps'] = array_filter($values['steps']);
        
		$recipe = Recipe::create($values);

		return redirect()->route('edit-recipe', ['id' => $recipe->id])->with('success', 'Recipe created!');
    }

    public function update($id, Request $request) {

        $values = array_filter($request->validate([
            'title' => 'required|string|max:255',
            'servings' => 'required|integer',
            'cook_time' => 'required|integer',
            'image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
            'attachment' => 'sometimes|nullable|mimes:pdf',
            'ingredients' => 'required|array|min:1',
            'steps' => 'required|array|min:1',
            'codes' => 'required|array'
        ]));


        if(array_key_exists("image", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['image']->extension();
            $values['image']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadSharedFile($name, 'recipes');
            $values['image'] = 'shared-'.$name;

        } else {
            unset($values['image']);
        }

        if(array_key_exists("attachment", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['attachment']->extension();
            $values['attachment']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadSharedFile($name, 'recipes');
            $values['attachment'] = 'shared-'.$name;

        } else {
            unset($values['attachment']);
        }

        //make sure to remove empty object from nested arrays
        $values['ingredients'] = array_filter($values['ingredients']);
        $values['steps'] = array_filter($values['steps']);
        
		$recipe = Recipe::where('id', $id)->firstOrFail();
		$recipe->update($values);

		return redirect()->route('edit-recipe', ['id' => $id])->with('success', 'Recipe updated!');
    }

}