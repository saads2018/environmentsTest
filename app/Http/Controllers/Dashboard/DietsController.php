<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Tenant\Diet;

use App\Helpers\AWSHelper;
use Illuminate\Http\Request;

class DietsController extends Controller
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

        $diets = Diet::get();
        return view('dashboard.diets.list')->with(compact('diets'));;
    }

    public function createForm() {
        $codes = \App\Enums\EducationCode::toReadableArray();
        $medCodes = array_map(function($key, $value) {
            return ["id" => $key, "value" => $value];
          }, array_keys($codes), $codes);
        return view('dashboard.diets.create')->with(compact('medCodes'));
    }

    public function view($id)
    {
        $diet = Diet::where('id', $id)->firstOrFail();
        $codes = \App\Enums\EducationCode::toReadableArray();
        $medCodes = array_map(function($key, $value) {
            return ["id" => $key, "value" => $value];
          }, array_keys($codes), $codes);

        return view('dashboard.diets.edit')->with(compact('diet', 'medCodes'));
    }

    public function create(Request $request) {

        $values = array_filter($request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
            'attachment' => 'sometimes|nullable|mimes:pdf',
            'days-morning-snack' => 'required|array|min:1',
            'days-breakfast' => 'required|array|min:1',
            'days-afternoon-snack' => 'required|array|min:1',
            'days-lunch' => 'required|array|min:1',
            'days-dinner' => 'required|array|min:1',
            'codes' => 'required|array'
        ]));

        $days = array("morning-snack", "breakfast", "afternoon-snack", "lunch", "dinner");
        foreach ($days as $i) {
            foreach ($values["days-".$i] as $k => $v) {
                $values['days'][$k][$i] = $v;
            }
        }


        if(array_key_exists("image", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['image']->extension();
            $values['image']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadSharedFile($name, 'diets');
            $values['image'] = 'shared-'.$name;

        } else {
            unset($values['image']);
        }

        if(array_key_exists("attachment", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['attachment']->extension();
            $values['attachment']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadSharedFile($name, 'diets');
            $values['attachment'] = 'shared-'.$name;

        } else {
            unset($values['attachment']);
        }

        $data = array();
		$data['days'] = $values['days'];
		$values['data'] = $data;
        
		$diet = Diet::create($values);

		return redirect()->route('edit-diet', ['id' => $diet->id])->with('success', 'Diet created!');
    }

    public function update($id, Request $request) {

        $values = array_filter($request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif',
            'attachment' => 'sometimes|nullable|mimes:pdf',
            'days-morning-snack' => 'sometimes|array|min:1',
            'days-breakfast' => 'sometimes|array|min:1',
            'days-afternoon-snack' => 'sometimes|array|min:1',
            'days-lunch' => 'sometimes|array|min:1',
            'days-dinner' => 'sometimes|array|min:1',
            'codes' => 'sometimes|array'
        ]));

        $days = array("morning-snack", "breakfast", "afternoon-snack", "lunch", "dinner");
        foreach ($days as $i) {
            foreach ($values["days-".$i] as $k => $v) {
                $values['days'][$k][$i] = $v;
            }
        }


        if(array_key_exists("image", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['image']->extension();
            $values['image']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadSharedFile($name, 'diets');
            $values['image'] = 'shared-'.$name;

        } else {
            unset($values['image']);
        }

        if(array_key_exists("attachment", $values)) {
            $name =  \Illuminate\Support\Str::uuid()->toString() . ".". $values['attachment']->extension();
            $values['attachment']->storeAs('uploads', $name);

            $helper = new AWSHelper;
            $helper->uploadSharedFile($name, 'diets');
            $values['attachment'] = 'shared-'.$name;

        } else {
            unset($values['attachment']);
        }

        $data = array();
		$data['days'] = $values['days'];
		$values['data'] = $data;
		
        
		$diet = Diet::where('id', $id)->firstOrFail();
		$diet->update($values);

		return redirect()->route('edit-diet', ['id' => $id])->with('success','Diet updated!');
    }

}