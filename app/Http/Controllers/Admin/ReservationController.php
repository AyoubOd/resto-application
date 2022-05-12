<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\TableStatus;
use App\Models\Reservation;
use App\Models\Table;
use App\Http\Requests\ReservationStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservations = Reservation::all();
        return view('admin.reservation.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tables = Table::where('status', TableStatus::Available)->get();
        return view('admin.reservation.create', compact('tables'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReservationStoreRequest $request)
    {
        $table = Table::findOrFail($request->table_id);
        if (($table->guest < $request->guest_number)) {
            return back()->withErrors('Please choose the table base on guests');
        }

        $request_date = Carbon::parse($request->res_date);
        foreach ($table->reservations as $res) {

            if (Carbon::parse($res->res_date)->format('Y-m-d') == $request_date->format('Y-m-d')) {
                return back()->withErrors('This table is reserved for this date.');
            }
        }
        Reservation::create($request->validated());

        return to_route('admin.reservation.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        $tables = Table::where('status', TableStatus::Available)->get();
        return view('admin.reservation.edit', compact('reservation', 'tables'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReservationStoreRequest $request, Reservation $reservation)
    {
        $table = Table::findOrFail($request->table_id);
        if (($table->guest < $request->guest_number)) {
            return back()->withErrors('Please choose the table base on guests');
        }

        $request_date = Carbon::parse($request->res_date);
        $reservations = Reservation::where('id', '!=', $reservation->id);
        foreach ($reservations as $res) {

            if (Carbon::parse($res->res_date)->format('Y-m-d') == $request_date->format('Y-m-d')) {
                return back()->withErrors('This table is reserved for this date.');
            }
        }
        $reservation->update($request->validated());

        return to_route('admin.reservation.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return to_route('admin.reservation.index')->with('msg', 'Reservation deleted successfully');
    }
}
