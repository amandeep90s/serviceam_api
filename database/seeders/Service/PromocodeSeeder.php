<?php

namespace Database\Seeders\Service;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromocodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($company = null): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('promocodes')->insert([
            [
                'company_id' => $company,
                'promo_code' => 'Service',
                'service' => 'SERVICE',
                'picture' => url('/') . '/images/common/promocodes/service.png',
                'percentage' => '10.00',
                'max_amount' => '12.00',
                'promo_description' => '10% off, Max discount is 12',
                'expiration' => '2019-11-15',
                'status' => 'ADDED'
            ]
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
