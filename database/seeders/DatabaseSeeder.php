<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Common\AdminSeeder;
use Database\Seeders\Common\AdminServiceSeeder;
use Database\Seeders\Common\CitySeeder;
use Database\Seeders\Common\CmsPageSeeder;
use Database\Seeders\Common\CommonClearSeeder;
use Database\Seeders\Common\CompanyCitySeeder;
use Database\Seeders\Common\CompanySeeder;
use Database\Seeders\Common\CountrySeeder;
use Database\Seeders\Common\DemoSeeder;
use Database\Seeders\Common\DocumentSeeder;
use Database\Seeders\Common\PermissionSeeder;
use Database\Seeders\Common\ProviderVehicleSeeder;
use Database\Seeders\Common\RoleSeeder;
use Database\Seeders\Common\SettingSeeder;
use Database\Seeders\Common\StateSeeder;
use Database\Seeders\Service\DisputeSeeder;
use Database\Seeders\Service\PromocodeSeeder;
use Database\Seeders\Service\ReasonSeeder;
use Database\Seeders\Service\ServiceCategorySeeder;
use Database\Seeders\Service\ServiceSeeder;
use Database\Seeders\Service\ServiceSubCategorySeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            AdminServiceSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            SettingSeeder::class,
            AdminSeeder::class,
            CompanyCitySeeder::class,
            DemoSeeder::class,
            ProviderVehicleSeeder::class,
            CmsPageSeeder::class,
            CommonClearSeeder::class,
            DocumentSeeder::class
        ]);


        //SERVICE
        $this->call([
            ServiceCategorySeeder::class,
            ServiceSubCategorySeeder::class,
            DisputeSeeder::class,
            ReasonSeeder::class,
            ServiceSeeder::class,
            PromocodeSeeder::class
        ]);

    }
}
