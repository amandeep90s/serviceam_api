<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeakHourRequest;
use App\Http\Requests\UpdatePeakHourRequest;
use App\Models\Common\PeakHour;

class PeakHourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeakHourRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PeakHour $peakHour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PeakHour $peakHour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePeakHourRequest $request, PeakHour $peakHour)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PeakHour $peakHour)
    {
        //
    }
}
