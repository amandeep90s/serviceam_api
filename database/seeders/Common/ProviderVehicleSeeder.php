<?php

namespace Database\Seeders\Common;

use App\Models\Common\Provider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProviderVehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($company = null): void
    {
        Schema::disableForeignKeyConstraints();

        $providers = Provider::where("company_id", $company)->get();

        foreach ($providers as $provider) {
            DB::table("provider_vehicles")->insert([
                [
                    //'vehicle_service_id' => $provider_vehicle->id,
                    "provider_id" => $provider->id,
                    "company_id" => $company,
                    "vehicle_model" => "BMW X6",
                    "vehicle_no" => "3D0979",
                    "vehicle_year" => "2019",
                    "vehicle_color" => "Black",
                    "vehicle_make" => "BMW",
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ],
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
