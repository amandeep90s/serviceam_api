<?php

namespace Database\Seeders\Service;

use App\Models\Common\CompanyCity;
use App\Models\Common\Provider;
use App\Models\Common\ProviderVehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($company = null): void
    {
        Schema::disableForeignKeyConstraints();

        $service_categories = DB::connection("service")
            ->table("service_categories")
            ->where("company_id", $company)
            ->get();

        $menus = [];

        foreach ($service_categories as $service_category) {
            $menus[] = [
                "bg_color" =>
                    "#" .
                    $this->random_color_part() .
                    $this->random_color_part() .
                    $this->random_color_part(),
                "icon" =>
                    url("/") .
                    "/images/menus/" .
                    strtolower(
                        str_replace(
                            " ",
                            "_",
                            $service_category->service_category_name
                        )
                    ) .
                    ".png",
                "title" => $service_category->service_category_name,
                "admin_service" => "SERVICE",
                "menu_type_id" => $service_category->id,
                "company_id" => $company,
                "sort_order" => 3,
            ];
        }

        DB::table("menus")->insert($menus);

        $company_cities = CompanyCity::where("company_id", $company)->get();

        $menu_city_data = [];
        $service_city_prices = [];
        $service_cities = [];

        $menu_list = DB::table("menus")
            ->where("company_id", $company)
            ->where("admin_service", "SERVICE")
            ->get();
        $service_list = DB::connection("service")
            ->table("services")
            ->where("company_id", $company)
            ->get();

        foreach ($company_cities as $company_city) {
            foreach ($menu_list as $menu) {
                $menu_city_data[] = [
                    "menu_id" => $menu->id,
                    "country_id" => $company_city->country_id,
                    "state_id" => $company_city->state_id,
                    "city_id" => $company_city->city_id,
                    "status" => "1",
                ];
            }

            foreach ($service_list as $service) {
                $service_cities[] = [
                    "company_id" => $company,
                    "service_id" => $service->id,
                    "country_id" => $company_city->country_id,
                    "city_id" => $company_city->city_id,
                ];

                $service_city_prices[] = [
                    "company_id" => $company,
                    "base_fare" => "50",
                    "country_id" => "50",
                    "city_id" => $company_city->city_id,
                    "service_id" => $service->id,
                    "fare_type" => "FIXED",
                    "commission" => "1",
                    "tax" => "1",
                    "fleet_commission" => "1",
                ];
            }
        }

        if (!empty($menu_city_data)) {
            foreach (array_chunk($menu_city_data, 1000) as $menu_city_datum) {
                DB::table("menu_cities")->insert($menu_city_datum);
            }
        }

        if (!empty($service_cities)) {
            foreach (array_chunk($service_cities, 1000) as $service_city) {
                DB::connection("service")
                    ->table("service_cities")
                    ->insert($service_city);
            }
        }

        if (!empty($service_city_prices)) {
            foreach (array_chunk($service_city_prices, 1000)
                     as $service_city_price) {
                DB::connection("service")
                    ->table("service_city_prices")
                    ->insert($service_city_price);
            }
        }

        $providers = Provider::where("company_id", $company)->get();

        foreach ($providers as $provider) {
            $provider_vehicle = ProviderVehicle::where(
                "provider_id",
                $provider->id
            )->first();
            $provider_services = DB::connection("service")
                ->table("services")
                ->where("company_id", $company)
                ->take("1")
                ->get();

            foreach ($provider_services as $provider_service) {
                DB::table("provider_services")->insert([
                    [
                        "provider_id" => $provider->id,
                        "company_id" => $company,
                        "admin_service" => "SERVICE",
                        "provider_vehicle_id" =>
                            $provider_vehicle != null
                                ? $provider_vehicle->id
                                : null,
                        "service_id" =>
                            $provider_service != null
                                ? $provider_service->id
                                : null,
                        "category_id" =>
                            $provider_service != null
                                ? $provider_service->service_category_id
                                : null,
                        "sub_category_id" =>
                            $provider_service != null
                                ? $provider_service->service_subcategory_id
                                : null,
                        "status" => "ACTIVE",
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ],
                ]);
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    public function random_color_part(): string
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, "0", STR_PAD_LEFT);
    }
}
