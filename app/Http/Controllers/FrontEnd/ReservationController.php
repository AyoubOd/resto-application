<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use App\Rules\DateBetween;
use App\Models\Reservation;
use App\Rules\TimeBetween;
use App\Models\Table;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function stepOne(Request $request)
    {
        $reservation = $request->session()->get('reservation');
        $min_date = Carbon::today()->format('Y-m-d\TH:i:s');
        $max_date = Carbon::now()->addWeek()->format('Y-m-d\TH:i:s');
        return view('reservations.step-one', compact('reservation', 'min_date', 'max_date'));
    }

    public function storeStepOne(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'tel_number' => 'required',
            'res_date' => ['required', new DateBetween, new TimeBetween],
            'guest_number' => 'required',
        ]);

        if (empty($request->session()->get('reservation'))) {
            $reservation = new Reservation;
            $reservation->fill($validated);
            $request->session()->put('reservation', $reservation);
        } else {
            $reservation = $request->session()->get('reservation');
            $reservation->fill($validated);
            $request->session()->put('reservation', $reservation);
        }

        return to_route('reservations.step.two');
    }

    public function stepTwo(Request $request)
    {
        $reservation = $request->session()->get('reservation');
        $reservedTables = Reservation::orderBy('res_date')->get()
            ->filter(function ($value) use ($reservation) {
                return date_create($value->res_date)
                    ->format('Y-m-d')
                    ==
                    date_create($reservation->res_date)
                    ->format('Y-m-d');
            })->pluck('table_id');

        $availableTables = Table::where('status', TableStatus::Available)
            ->where('guest', '>=', $reservation->guest_number)
            ->whereNotIn('id', $reservedTables)
            ->get();

        return view('reservations.step-two', compact('reservation', 'availableTables'));
    }


    public function storeStepTwo(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required'
        ]);

        $reservation = $request->session()->get('reservation');
        $reservation->fill($validated);
        $reservation->save();
        $request->session()->forget('reservation');

        return to_route('thankyou');
    }
}
