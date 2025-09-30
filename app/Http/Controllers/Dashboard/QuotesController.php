<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Tenant\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;


class QuotesController extends Controller
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

        $quotes = Quote::get();
        return view('dashboard.quotes.list')->with(compact('quotes'));;
    }

    public function view($id)
    {
        $quote = Quote::where('id', $id)->firstOrFail();
        return view('dashboard.quotes.edit')->with(compact('quote'));
    }

    public function update($id, Request $request) {

        $values = array_filter($request->validate([
            'text' => 'required|string|max:255',
        ]));

		$quote = Quote::where('id', $id)->firstOrFail();
		$quote->update($values);

		return redirect()->route('quote.list')->with('success', 'Quote updated!');;
    }

    public function create(Request $request) {

        $values = array_filter($request->validate([
            'text' => 'required|array|max:255',
        ]));
        $latestQuote = Quote::orderBy('scheduled_at','DESC')->first();
		if($latestQuote == null) {
			$latestQuoteSchedule = now()->copy()->addWeeks(-1)->startOfWeek();
		} else {
			$latestQuoteSchedule = $latestQuote->scheduled_at; //get max scheduled_at + one week
		}
        $arr = array();
        foreach ($values['text'] as $key => $value) {
			if(!$value){
				continue;
			}
			$schedule = $latestQuoteSchedule->copy()->addWeeks(1);
			$latestQuoteSchedule = $schedule;
			array_push($arr, ['text' => $value, 'id' => Str::uuid()->toString(), 'scheduled_at' => $schedule]);
		}

        try {
			$count = Quote::upsert($arr, ['id']);
		} catch(Exception $ex) {}

		return redirect()->route('quote.list')->with('success', 'Quotes created!');;
    }

}