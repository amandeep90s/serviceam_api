<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequestPaymentRequest;
use App\Http\Requests\UpdateServiceRequestPaymentRequest;
use App\Models\Service\ServiceRequestPayment;

class ServiceRequestPaymentController extends Controller
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
    public function store(StoreServiceRequestPaymentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceRequestPayment $serviceRequestPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceRequestPayment $serviceRequestPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequestPaymentRequest $request, ServiceRequestPayment $serviceRequestPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceRequestPayment $serviceRequestPayment)
    {
        //
    }
}
