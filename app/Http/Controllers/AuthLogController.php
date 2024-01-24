<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthLogRequest;
use App\Http\Requests\UpdateAuthLogRequest;
use App\Models\Common\AuthLog;

class AuthLogController extends Controller
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
    public function store(StoreAuthLogRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AuthLog $authLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AuthLog $authLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthLogRequest $request, AuthLog $authLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuthLog $authLog)
    {
        //
    }
}
