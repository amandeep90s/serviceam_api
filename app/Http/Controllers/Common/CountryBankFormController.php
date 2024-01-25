<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCountryBankFormRequest;
use App\Http\Requests\UpdateCountryBankFormRequest;
use App\Models\Common\CountryBankForm;

class CountryBankFormController extends Controller
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
    public function store(StoreCountryBankFormRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CountryBankForm $countryBankForm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CountryBankForm $countryBankForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountryBankFormRequest $request, CountryBankForm $countryBankForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CountryBankForm $countryBankForm)
    {
        //
    }
}
