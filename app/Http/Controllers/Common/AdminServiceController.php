<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminServiceRequest;
use App\Http\Requests\UpdateAdminServiceRequest;
use App\Models\Common\AdminService;

class AdminServiceController extends Controller
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
    public function store(StoreAdminServiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminService $adminService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminService $adminService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminServiceRequest $request, AdminService $adminService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminService $adminService)
    {
        //
    }
}
