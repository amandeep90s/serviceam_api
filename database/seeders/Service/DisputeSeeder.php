<?php

namespace Database\Seeders\Service;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DisputeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($company = null): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table("disputes")->insert([
            [
                "service" => "SERVICE",
                "dispute_type" => "user",
                "dispute_name" => "Provider asked extra amount",
                "status" => "active",
                "admin_services" => "SERVICE",
                "company_id" => $company,
            ],
            [
                "service" => "SERVICE",
                "dispute_type" => "provider",
                "dispute_name" => "Customer denied to pay amount",
                "status" => "active",
                "admin_services" => "SERVICE",
                "company_id" => $company,
            ],
            [
                "service" => "SERVICE",
                "dispute_type" => "user",
                "dispute_name" => "My wallet amount does not deducted",
                "status" => "active",
                "admin_services" => "SERVICE",
                "company_id" => $company,
            ],
            [
                "service" => "SERVICE",
                "dispute_type" => "user",
                "dispute_name" => "Promocode amount does not reduced",
                "status" => "active",
                "admin_services" => "SERVICE",
                "company_id" => $company,
            ],
            [
                "service" => "SERVICE",
                "dispute_type" => "user",
                "dispute_name" => "Provider incompleted the service",
                "status" => "active",
                "admin_services" => "SERVICE",
                "company_id" => $company,
            ],
            [
                "service" => "SERVICE",
                "dispute_type" => "provider",
                "dispute_name" => "User provided wrong service information",
                "status" => "active",
                "admin_services" => "SERVICE",
                "company_id" => $company,
            ],
            [
                "service" => "SERVICE",
                "dispute_type" => "provider",
                "dispute_name" => "User neglected to pay additional charge",
                "status" => "active",
                "admin_services" => "SERVICE",
                "company_id" => $company,
            ],
            [
                "service" => "SERVICE",
                "dispute_type" => "provider",
                "dispute_name" => "User provided less amount",
                "status" => "active",
                "admin_services" => "SERVICE",
                "company_id" => $company,
            ],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
